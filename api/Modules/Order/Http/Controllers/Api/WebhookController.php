<?php namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Traits\OnepayTrait;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\WebhookRepository;

/**
 * Class WebhookController
 * @package Modules\Order\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-10
 */
class WebhookController extends ApiBaseModuleController {
    use OnepayTrait;

    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    public function __construct(Request $request, WebhookRepository $order_webhook_repository, OrderRepository $order_repository) {
        $this->model_repository = $order_webhook_repository;
        $this->order_repository = $order_repository;

        $this->middleware('auth.usr')->except(['onepayIPN']);

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_webhook",
     *   summary="Get Webhooks",
     *   operationId="getWebhooks",
     *   tags={"BackendOrdWebhooks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function index() {
        try {
            // Check permission
            if (!$this->isCRUD('exchange_webhook', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            if (!$page) $page = 1;
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = !$order ? 'desc' : strtolower($order);
            $queries = [
                'and'        => [],
                'orWhereRaw' => [],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)->skip($pageSize * ($page - 1))
                ->get();
            $output = [];
            foreach ($results as $result) {
                if (is_string($result->data)) $result->data = json_decode($result->data);
                $output[] = $result;
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_webhook/{id}",
     *   summary="Get Webhook",
     *   operationId="getWebhook",
     *   tags={"BackendOrdWebhooks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Webhook Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function show($id) {
        try {
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();

            if (is_string($model->data)) $model->data = json_decode($model->data);

            if (!$model) return $this->errorNotFound();
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/ord_webhook/{id}",
     *   summary="Delete Webhook",
     *   operationId="deleteWebhook",
     *   tags={"BackendOrdWebhooks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Webhook Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);
            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    private function handleWebhook($model) {
        if ($model->type == 'viettelpost') {
            if (!empty($model->data['DATA']) && is_array($model->data['DATA']) && !empty($model->data['DATA']['ORDER_NUMBER']) && isset($model->data['DATA']['ORDER_STATUS'])) {
                $data = [];
                foreach ($model->data['DATA'] as $k => $v) $data[strtolower($k)] = $v;
                /*$order_shipping = $this->order_shipping_repository->findByAttributes(['order_number' => $data['order_number']]);
                if ($order_shipping) {
                    $order = $order_shipping->order;
                    $data['order_id'] = $order_shipping->order_id;
                    $this->order_shipping_history_repository->create($data);
                    // Check status success
                    if ($data['order_status'] == 501) {
                        // Update order status
                        if ($order->shipping_status != SHIPPING_SS_DELIVERED) {
                            if ($order->payment_status == PAYMENT_SS_PAID) { // Update order status
                                $this->order_repository->update($order, ['shipping_status' => SHIPPING_SS_DELIVERED, 'order_status' => ORDER_SS_COMPLETED]);
                            }
                            // Send email order shipping success
                            // code ???
                        }
                    }
                } else {
                    //$this->model_repository->destroy($model);
                }*/
            } else {
                //$this->model_repository->destroy($model);
            }
        } else if ($model->type == 'onepay') { // Onepay IPN Fix
           // Fix code
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_webhook/{id}/fix",
     *   summary="Fix Webhook",
     *   operationId="ordFixWebhook",
     *   tags={"BackendOrdWebhooks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Webhook Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function onepayIPNFix($id) {
        try {
            $model = $this->model_repository->find($id);
            if ($model) $this->handleWebhook($model);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
