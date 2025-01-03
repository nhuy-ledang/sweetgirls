<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

//[
//	"order_number" => "18466196770",
//	"order_reference" => "ST-INV-0011#29",
//	"order_statusdate" => "19/07/2023 12:13:04",
//	"order_status" => int(107),
//	"status_name" => "Đối tác yêu cầu hủy qua API",
//	"localion_currently" => NULL,
//	"note" => "test",
//	"money_collection" => int(2278000),
//	"money_feecod" => int(0),
//	"money_totalfee" => int(29091),
//	"money_total" => int(32000),
//	"expected_delivery" => NULL,
//	"product_weight" => int(300),
//	"order_service" => string(4) "LCOD",
//	"order_payment" => int(2),
//	"expected_delivery_date" => NULL,
//	"detail" => [],
//	"voucher_value" => int(0),
//	"money_collection_origin" => NULL,
//	"employee_name" => NULL,
//	"employee_phone" => NULL,
//	"is_returning" => bool(false),
//]
class OrderShippingHistory extends CoreModel {
    private $statuses = [
        '100' => 'Tạo đơn hàng',
        '101' => 'ViettelPost yêu cầu hủy đơn hàng',
        '102' => 'Đơn hàng chờ xử lý',
        '103' => 'Giao cho bưu cục',
        '104' => 'Giao cho Bưu tá đi nhận',
        '105' => 'Buu Tá đã nhận hàng',
        '106' => 'Đối tác yêu cầu lấy lại hàng',
        '107' => 'Đối tác yêu cầu hủy qua API',
        '200' => 'Nhận từ bưu tá - Bưu cục gốc',
        '201' => 'Hủy nhập phiếu gửi',
        '202' => 'Sửa phiếu gủi',
        '300' => 'Đóng bảng kê đi',
        '301' => 'Ðóng túi gói',
        '302' => 'Đóng Chuyến thư',
        '303' => 'Đóng tuyến xe',
        '400' => 'Nhận bảng kê đến',
        '401' => 'Nhận Túi gói',
        '402' => 'Nhận chuyến thư',
        '403' => 'Nhận chuyến xe',
        '500' => 'Giao bưu tá đi phát',
        '501' => 'Thành công - Phát thành công',
        '502' => 'Chuyển hoàn bưu cục gốc',
        '503' => 'Hủy - Theo yêu cầu khách hàng',
        '504' => 'Thành công - Chuyển trả người gửi',
        '505' => 'Tồn - Thông báo chuyển hoàn bưu cục gốc',
        '506' => 'Tồn - Khách hàng nghỉ, không có nhà',
        '507' => 'Tồn - Khách hàng đến bưu cục nhận',
        '508' => 'Phát tiếp',
        '509' => 'Chuyển tiếp bưu cục khác',
        '510' => 'Hủy phân công phát',
        '515' => 'Duyệt hoàn',
        '550' => 'Phát tiếp',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__shipping_histories';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id', 'order_number', 'order_status', 'note', 'money_collection', 'money_feecod', 'money_totalfee', 'money_total', 'product_weight', 'order_service', 'order_payment', 'voucher_value', 'employee_name', 'employee_phone', 'localion_currently'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['order_status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'order_id'         => 'integer',
        'order_status'     => 'integer',
        'money_collection' => 'double',
        'money_feecod'     => 'double',
        'money_totalfee'   => 'double',
        'money_total'      => 'double',
        'product_weight'   => 'double',
        'order_payment'    => 'integer',
    ];

    public function getOrderStatusNameAttribute() {
        return (!empty($this->order_status) && isset($this->statuses[$this->order_status])) ? $this->statuses[$this->order_status] : '';
    }

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order');
    }

    public function order_shipping() {
        return $this->belongsTo('\Modules\Order\Entities\OrderShipping', 'order_number', 'order_number');
    }
}
