<?php namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Order\Repositories\InvoiceVatRepository;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Repositories\OrderRepository;

/**
 * Class InvoiceVatController
 *
 * @package Modules\Order\Http\Controllers\Api
 */
class InvoiceVatController extends ApiBaseModuleController {
    /**
     * @var string
     */
    protected $module_id = 'sales';

    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $model_repository;

    /**
     * @var \Modules\Order\Repositories\OrderProductRepository
     */
    protected $order_product_repository;

    /**
     * @var \Modules\Order\Repositories\InvoiceVatRepository
     */
    protected $invoice_vat_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                OrderProductRepository $order_product_repository,
                                InvoiceVatRepository $invoice_vat_repository) {
        $this->model_repository = $order_repository;
        $this->invoice_vat_repository = $invoice_vat_repository;
        $this->order_product_repository = $order_product_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_invoices/{id}/vat",
     *   summary="Create Invoice VAT",
     *   operationId="ordCreateInvoiceVAT",
     *   tags={"BackendOrdInvoices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Invoice Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function createVATMisa($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if (!$model->is_invoice) return $this->errorWrongArgs('invoice.vat.not_export');
            $order = false;//$model->order_id ? $model->order : null;
            $user = $model->user;

            $order_products = $this->order_product_repository->getModel()->where('order_id', $id)->get();
            $order_products_map = [];
            if ($order_products) $order_products_map = array_column(json_decode($order_products, true), null, 'product_id');

            // Generate params
            $sort_order = 0;
            $SortOrderView = 0;
            $detail = [];
            foreach ($model->products as $k => $product) {
                $product_info_extra = [];
                if (isset($order_products_map[$product->id])) {
                    $product_info_extra = $order_products_map[$product->id];
                }

                if ($product_info_extra['type'] == 'T' && $product_info_extra['total'] == 0) {
                    $InventoryItemType = 4;
                    $SortOrderView = null;
                } elseif ($product_info_extra['type'] == 'I') {
                    $InventoryItemType = 3;
                    $SortOrderView = null;
                } elseif ($product_info_extra['type'] == 'G') {
                    $InventoryItemType = 2;
                } else {
                    $InventoryItemType = 1;
                }

                $sort_order++;

                $detail[] = [
                    'RefDetailID'       => $product->id, // ID của dòng hàng hóa | Guild | Yes
                    'RefID'             => $model->no, // ID của hóa đơn ở master | Guild | Yes
                    'InventoryItemType' => $InventoryItemType, // Loại hàng hóa: Product = 1, Promotion = 2, Description = 3, Discount = 4 | int | Yes
                    'InventoryItemCode' => $product_info_extra['model'], // Mã hàng hóa | string | Yes
                    'Description'       => $product_info_extra['name'], // Tên hàng hóa | string | Yes
                    'UnitName'          => 'dvt', // đơn vị tính | string | Yes
                    'Quantity'          => $product_info_extra['quantity'], // số lượng | decimal | Yes
                    'UnitPrice'         => $product_info_extra['price'], // đơn giá | decimal | Yes
                    'AmountOC'          => (int)$product_info_extra['quantity'] * (int)$product_info_extra['price'], // thành tiền nguyên tệ = Quantity * UnitPrice | decimal | Yes
                    'Amount'            => (int)$product_info_extra['quantity'] * (int)$product_info_extra['price'], // thành tiền quy đổi = AmountOC * ExchangeRate | decimal | Yes
                    'DiscountRate'      => 0, // phần trăm chiết khấu | decimal | Yes
                    'DiscountAmountOC'  => 0, // số tiền chiết khấu nguyên tệ | decimal | Yes
                    'DiscountAmount'    => 0, // số tiền chiết khấu quy đổi = DiscountAmountOC * ExchangeRate | decimal | Yes
                    'VATRate'           => 0, // thuế suất | decimal | Yes
                    'VATAmountOC'       => 0, // tiền thuế VAT nguyên tệ = AmountOC * VATRate/100 | decimal | Yes
                    'VATAmount'         => 0, // tiền thuế VAT quy đổi = VATAmountOC * ExchangeRate | decimal | Yes
                    'SortOrder'         => $sort_order, // số thứ tự | int | Yes
                    'SortOrderView'     => is_null($SortOrderView) ? $SortOrderView : $sort_order, // số thứ tự hiển thị(lưu ý: đối với hàng hóa InventoryItemType:3,4 thì SortOrderView:null | int | Yes
                ];
            }

            $discount = $model->discount_total ? $model->discount_total : $model->voucher_total;
            $discountRate = $discount ? ($discount / $model->sub_total) * 100 : 0;

            $params = [
                'RefID'                 => $model->no, // Khóa chính của hóa đơn | Guid | Yes
                'InvDate'               => date('Y-m-d'), // Ngày hóa đơn | Datetime | Yes
                'TypeDiscount'          => $discount ? 2 : 0, // Loại chiết khấu: 0: Không có chiết khấu 1: Chiết khấu theo dòng hàng 2: Chiết khấu theo tổng giá trị hóa đơn | int | Yes
                'DiscountRate'          => $discountRate, // Phần trăm chiết khấu | decimal | Yes
                'TotalSaleAmountOC'     => $model->sub_total, // = Sum(AmountOC, InventoryItemType = 0) - Sum(AmountOC, InventoryItemType = 4) | decimal | yes
                'TotalSaleAmount'       => $model->sub_total, // = TotalSaleAmountOC * ExchangeRate | decimal | yes
                'TotalVATAmountOC'      => 0, // = Sum(VATAmountOC, InventoryItemType = 0) - Sum(VATAmountOC, InventoryItemType = 4) | decimal | yes
                'TotalVATAmount'        => 0, // = TotalVATAmountOC * ExchangeRate | decimal | yes
                'TotalDiscountAmountOC' => $discount, // = Sum(DiscountAmountOC) | decimal | yes
                'TotalDiscountAmount'   => $discount, // = TotalDiscountAmountOC * ExchangeRate | decimal | yes
                'TotalAmountOC'         => $model->total, // = TotalSaleAmountOC – TotalDiscountAmountOC + TotalVATAmountOC | decimal | yes
                'TotalAmount'           => $model->total, // = TotalAmountOC * ExchangeRate | decimal | No
                'InvoiceDetails'          => $detail,
            ];
            if ($model->is_invoice) {
                $params = array_merge($params, [
                    'AccountObjectTaxCode'  => $model->company_tax, // Mã số thuế của KH | string | No
                    'AccountObjectName'     => $model->company, // Tên đơn vị | string | No
                    'AccountObjectCode'     => $user->no, // Mã KH | string | No
                    'ContactName'           => $model->shipping_first_name, // Người mua hàng | string | No
                    'ReceiverEmail'         => $model->email, // email người mua hàng | string | No
                    'ReceiverName'          => $model->shipping_first_name, // Tên người nhận hóa đơn | string | No
                    'ReceiverMobile'        => $model->shipping_phone_number ? $model->shipping_phone_number : $model->phone_number, // Số điện thoại người mua hàng | string | No
                    'PaymentMethod'         => $model->payment_code == 'cod' ? 'TM' : 'CK', // Hình thức thanh toán (TM,Ck,TM/CK,...) | string | yes
                ]);
            }

            $invoice_vat = new \Modules\Order\Invoice\Invoice();
            if (!$model->id_attr) {
                $r = $invoice_vat->createInvoice($params);
                return $this->respondWithError(['data' => $r]);
            } else {
                $r = $invoice_vat->updateInvoice($model->id_attr, $params);
            }
            if ($r && $r['status'] == 200) {
                $invoice_vat = $this->invoice_vat_repository->create([
                    'invoice_id'   => $model->no,
                    'id_attr'      => $model->no,
                    'date_release' => date('Y-m-d'),
                    'vat_amount'   => 0,
                    'total'        => $model->total,
                    'amount'       => $model->total,
                ]);
                $model = $this->model_repository->update($model, ['invoiced' => 1, 'invoice_no' => $invoice_vat->id]);
                return $this->respondWithSuccess($model, ['invoice_res' => $r]);
            } else {
                return $this->respondWithErrorKey('system.wrong_arguments', 400, ($r && !empty($r['error']) ? $r['error'] : ''), [], ['invoice_res' => $r, 'model' => $model]);
            }
            //return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function createVATPaviet($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if (!$model->is_invoice) return $this->errorWrongArgs('invoice.vat.not_export');
            $order = false;//$model->order_id ? $model->order : null;
            $contract = $model->contract;
            $customer = $model->customer;
            // Generate params
            $discount = 0;
            $detail = [];
            foreach ($model->products as $k => $p) {
                $product = $p->product;
                $detail[] = [
                    'num'            => $k + 1, // int Số thứ tự các mục hàng hoá
                    'name'           => $p->name, // string	Tên hàng hoá
                    'code'           => $product ? $product->idx : '', // string Mã sản phẩm
                    'unit'           => $p->unit_name, // string Đơn vị tính
                    'quantity'       => $p->quantity, // double ố lượng
                    'price'          => $p->pretax, // string Đơn giá hàng hoá
                    'total'          => $p->pretax * $p->quantity, // string Thành tiền (=price* quantity)
                    'discount'       => $p->discount, // int % chiết khấu
                    'discountAmount' => $p->discount_value, // string Số tiền chiết khấu
                    'feature'        => 1, // int Tính chất - 1: HH, DV, 2: KM, 3: CK, 4: Ghi chú
                    'vatRate'        => $p->total == 0 ? -1 : $p->vat, // % thuế từng mục hàng hoá
                    'vatAmount'      => $p->vat_value, // Tiền thuế VAT của sản phẩm/hàng hoá
                    'amount'         => $p->total,
                ];
                if ($p->discount_value) $discount = 1;
            }
            $params = [
                'date_export'     => date('Y-m-d'),
                //'order_code'      => $model->no,
                'discount'        => $discount,
                'vat_rate'        => -1,
                'vat_amount'      => $model->vat_value, // Tiền thuế GTGT của hàng hóa
                'total'           => $model->sub_total, // Tổng tiền hàng hoá chưa thuế
                'amount'          => $model->total, // Tổng tiền hàng hoá đã cộng tiền thuế và các phí khác (nếu có)
                'amount_in_words' => ucfirst(number_format_vnd($model->total)),
                'payment_type'    => $model->payment_method == 'bank_transfer' ? 2 : 1, // 1: Tiền mặt 2: Chuyển khoản, 3: Tiền mặt/chuyển khoản, 4: Đối trừ công nợ, 5: Không thu tiền, 6: khác
                'detail'          => $detail,
            ];
            $params = array_merge($params, [
                //'customer_code' => $customer->idx,
                'cus_taxCode' => $customer->tax, // MST người mua - min:10 ký tự, max:14 ký tự
                //'cus_buyer', // Tên người mua hàng
                'cus_name'    => $customer->company, // Tên đơn vị
                'cus_address' => $customer->business_address ? $customer->business_address : $customer->company_address, // Địa chỉ
                //'cus_bank_no'   => $model->bank_number, // Số tài khoản ngân hàng: max 30 ký tự
                //'cus_bank_name' => $model->bank_name, // Tên ngân hàng
                //'cus_phone'     => $customer->company_phone, // Điện thoại: max 20 ký tự
                //'cus_email'     => $customer->company_email, // Email người mua hàng: max 50 ký tự
            ]);
            $invoice_vat = new \Modules\Order\Invoice\Invoice();
            if (!$model->id_attr) {
                $r = $invoice_vat->createInvoice($params);
            } else {
                $r = $invoice_vat->updateInvoice($model->id_attr, $params);
            }
            if ($r && $r['status'] == 200) {
                $model = $this->model_repository->update($model, ['id_attr' => $r['id_attr'], 'date_export' => $params['date_export']]);
                return $this->respondWithSuccess($model, ['invoice_res' => $r]);
            } else {
                return $this->respondWithErrorKey('system.wrong_arguments', 400, ($r && !empty($r['message']) ? $r['message'] : ''), [], ['invoice_res' => $r, 'model' => $model]);
            }
            //return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
