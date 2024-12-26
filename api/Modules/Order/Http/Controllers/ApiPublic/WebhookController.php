<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Core\Traits\OnepayTrait;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\OrderShippingHistoryRepository;
use Modules\Order\Repositories\OrderShippingRepository;
use Modules\Order\Repositories\WebhookRepository;
use Modules\Stock\Repositories\RequestRepository;
use Modules\System\Repositories\SettingRepository;

/**
 * Class WebhookController
 * @package Modules\Order\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-10
 */
class WebhookController extends ApiBaseModuleController {
    use OnepayTrait;

    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    /**
     * @var \Modules\Order\Repositories\OrderShippingRepository
     */
    protected $order_shipping_repository;

    /**
     * @var \Modules\Order\Repositories\OrderShippingHistoryRepository
     */
    protected $order_shipping_history_repository;

    /**
     * @var \Modules\Stock\Repositories\RequestRepository;
     */
    protected $sto_request_repository;

    public function __construct(Request $request, WebhookRepository $order_webhook_repository,
                                OrderRepository $order_repository,
                                OrderShippingRepository $order_shipping_repository,
                                OrderShippingHistoryRepository $order_shipping_history_repository,
                                RequestRepository $sto_request_repository,
                                SettingRepository $setting_repository
    ) {
        $this->model_repository = $order_webhook_repository;
        $this->order_repository = $order_repository;
        $this->order_shipping_repository = $order_shipping_repository;
        $this->order_shipping_history_repository = $order_shipping_history_repository;
        $this->sto_request_repository = $sto_request_repository;
        $this->setting_repository = $setting_repository;

        parent::__construct($request);
    }

    private function handleWebhook($model) {
        if ($model->type == 'viettelpost') {
            if (!empty($model->data['DATA']) && is_array($model->data['DATA']) && !empty($model->data['DATA']['ORDER_NUMBER']) && isset($model->data['DATA']['ORDER_STATUS'])) {
                $data = [];
                foreach ($model->data['DATA'] as $k => $v) $data[strtolower($k)] = $v;
                $order_shipping = $this->order_shipping_repository->findByAttributes(['order_number' => $data['order_number']]);
                if ($order_shipping) {
                    $order = $order_shipping->order;
                    $data['order_id'] = $order_shipping->order_id;
                    $this->order_shipping_history_repository->create($data);
                    $sto_request = $this->sto_request_repository->getModel()->where('invoice_id', $order->id)->orderBy('id', 'desc')->first();

                    // Being transported
                    if ($data['order_status'] == 200) {
                        if ($order->shipping_status == SHIPPING_SS_CREATE_ORDER || empty($order->shipping_status)) {
                            $result = $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_DELIVERING]);
                        }
                        // Change Stock Request
                        if ($sto_request) $this->sto_request_repository->update($sto_request, ['shipping_status' => STO_SHIPPING_SS_DELIVERING, 'status' => STO_REQUEST_SS_COMPLETED]);
                    }
                    // Check status success
                    if ($data['order_status'] == 501) {
                        // Update order status
                        if ($order->shipping_status != SHIPPING_SS_DELIVERED) {
                            if ($order->payment_status == PAYMENT_SS_PAID) { // Update order status
                                $result = $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_DELIVERED, 'order_status' => ORDER_SS_COMPLETED]);
                            } else {
                                $result = $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_DELIVERED, 'order_status' => ORDER_SS_COMPLETED, 'payment_status' => PAYMENT_SS_PAID, 'payment_at' => date('Y-m-d H:i:s')]);
                                // Send link wheel - Tạm thời
                                if ($order->email && $this->setting_repository->findByKey('config_wheel_order_status')) {
                                    $data = app('\Modules\Order\Http\Controllers\Api\OrderController')->getOrderProducts($order);
                                    $data['link'] = config('app.url') . ($this->locale == 'en' ? '/en' : '') . "/checkout/success?id={$order->id}&s={$order->order_status}";
                                    $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
                                    dispatch(new \Modules\Order\Jobs\OrderWheelJob($this->email, $data));
                                }
                            }
                            if (isset($result) && ($order->order_status != ORDER_SS_COMPLETED || $order->order_status != ORDER_SS_CANCELED)) {
                                app('\Modules\Order\Http\Controllers\Api\OrderController')->handleReward($result);
                            }
                            // Send email order shipping success
                            // code ???

                            // Change Stock Request
                            if ($sto_request) $this->sto_request_repository->update($sto_request, ['shipping_status' => STO_SHIPPING_SS_COMPLETED, 'status' => STO_REQUEST_SS_COMPLETED]);
                        }
                    }
                    // Check status return
                    if ($data['order_status'] == 504) {
                        // Update order status
                        if ($order->shipping_status != SHIPPING_SS_DELIVERED) {
                            if ($order->payment_status == PAYMENT_SS_PAID) { // refunded ???
                                $result = $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_RETURN, 'order_status' => ORDER_SS_RETURNED]);
                            } else {
                                $result = $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_RETURN, 'order_status' => ORDER_SS_RETURNED]);
                            }
                        }
                        // Change Stock Request
                        if ($sto_request) $this->sto_request_repository->update($sto_request, ['shipping_status' => STO_SHIPPING_SS_DELIVERING, 'status' => STO_REQUEST_SS_REVOKE]);
                        // Cộng lại product vào kho
                        // ???
                    }
                } else {
                    //$this->model_repository->destroy($model);
                }
            } else {
                $this->model_repository->destroy($model);
            }
        } else if ($model->type == 'onepay') {
            if (!empty($model->data) && is_array($model->data) && !empty($model->data['vpc_OrderInfo']) && !empty($model->data['vpc_Message'])) {
                $id = false;
                $order = false;
                $vpc_OrderInfo = $model->data['vpc_OrderInfo'];
                $vpc_Message = $model->data['vpc_Message'];
                if ($vpc_OrderInfo && $vpc_Message) $id = $vpc_OrderInfo;
                if ($id) $order = $this->order_repository->find($id);
                if ($order) {
                    if (strtotime($order->created_at) + 15 * 60 >= time()) { // Check 15 minute
                        if ($order->payment_status != PAYMENT_SS_PAID && in_array($order->payment_code, [PAYMENT_MT_DOMESTIC, PAYMENT_MT_FOREIGN, PAYMENT_MT_MOMO])) {
                            $allInput = $model->data;
                            $transactionNo = $model->data['vpc_TransactionNo'];
                            $txnResponseCode = $model->data['vpc_TxnResponseCode'];
                            app('\Modules\Order\Http\Controllers\ApiPublic\OrderController')->handleWebhook($order, $allInput, $transactionNo, $txnResponseCode);
                        }
                    }
                }
            }
        }
    }

    /**
     * @OA\Post(
     *   path="/webhook/onepay/ipn",
     *   summary="Webhook Onepay IPN",
     *   operationId="webhookOnepayIPN",
     *   tags={"Webhooks"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function onepayIPN() {
        try {
            // Disable debug
            if (config('app.debug')) app('debugbar')->disable();
            $allInput = $this->request->all();
            // Create Model
            $model = $this->model_repository->create(['type' => 'onepay', 'data' => $allInput, 'ip' => $this->request->server->get('REMOTE_ADDR')]);
            $this->handleWebhook($model);

            return response('responsecode=1');
        } catch (\Exception $e) {
            return response('responsecode=0');
        }
    }

    /**
     * @OA\Post(
     *   path="/webhook/viettelpost",
     *   summary="Webhook Viettel Post",
     *   operationId="webhookViettelPost",
     *   tags={"Webhooks"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function viettelpost() {
        try {
            // Disable debug
            if (config('app.debug')) app('debugbar')->disable();
            $allInput = $this->request->all();
            // Create Model
            $model = $this->model_repository->create(['type' => 'viettelpost', 'data' => $allInput, 'ip' => $this->request->server->get('REMOTE_ADDR')]);
            $this->handleWebhook($model);

            // Dispatch webhook
            dispatch(new \Modules\Order\Jobs\ProcessWebhookJob($allInput));

            return response('responsecode=1');
        } catch (\Exception $e) {
            return response('responsecode=0');
        }
    }

    /**
     * @OA\Get(
     *   path="/webhook/update_viettelpost",
     *   summary="Webhook Update Viettel Post",
     *   operationId="webhookUpdateViettelPost",
     *   tags={"Webhooks"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function updateViettelpost() {
        try {
            // Create Model
            $results = $this->model_repository->getModel()->where('type', 'onepay')->get();
            foreach ($results as $model) {
                $this->handleWebhook($model);
            }

            return $this->respondWithSuccess([]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
