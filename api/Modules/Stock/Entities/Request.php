<?php namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;
use Modules\Order\Entities\Order;

/***
 * Class Request
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Request extends CoreModel {
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__requests';

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
    protected $fillable = ['idx', 'owner_id', 'stock_id', 'invoice_id', 'type', 'in_type', 'shipping_status', 'status', 'platform', 'attach', 'location',
        'in_stock_id', 'out_type', 'out_stock_id', 'department_id', 'customer_id', 'carrier_id', 'storekeeper_id', 'st_manager_id',
        'total', 'content', 'note', 'reviewer_id', 'approved_at', 'rejected_at', 'deadline_at', 'estimate_at', 'reality_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['statuss', 'in_types', 'out_types', 'shipping_statuss', 'invoice'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                    => 'integer',
        'total'                 => 'double',
        'owner_id'              => 'integer',
        'stock_id'              => 'integer',
        'in_stock_id'           => 'integer',
        'out_stock_id'          => 'integer',
        'department_id'         => 'integer',
        'customer_id'           => 'integer',
        'carrier_id'            => 'integer',
        'storekeeper_id'        => 'integer',
        'st_manager_id'         => 'integer',
        'reviewer_id'           => 'integer',
        'shipping_status'       => 'integer',
        'in_type'               => 'integer',
        'out_type'              => 'integer',
        'status'                => 'integer',
    ];

    public function getStatussAttribute() {
        $list = [
            'pending' => ['name' => 'Chờ xử lý', 'color' => '#677788'],
            'adjust'  => ['name' => 'Cần điều chỉnh', 'color' => '#FF802C'],
            'in_process'  => ['name' => 'Đang xử lý', 'color' => '#FF802C'],
            'completed'   => ['name' => 'Duyệt triển khai', 'color' => '#32D593'],
            'rejected' => ['name' => 'Từ chối', 'color' => '#FF4A65'],
            'stored' => ['name' => 'Lưu trữ', 'color' => '#6906A2'],
            'canceled' => ['name' => 'Đã hủy', 'color' => '#172228'],
        ];
        if ($this->type == 'out') {
            //$list['in_process'] = ['name' => 'Chờ xuất kho', 'color' => '#3986FF'];
            //$list['completed'] = ['name' => 'Đã xuất kho', 'color' => '#32D593'];
        }
        return (!empty($this->status) && isset($list[$this->status])) ? $list[$this->status] : '';
    }

    public function getInTypesAttribute() {
        $list = [
            'purchase' => ['name' => 'Mua vào', 'color' => '#3986FF'],
            'produce'  => ['name' => 'Tự sản xuất', 'color' => '#FF4A65'],
            'return'   => ['name' => 'Hàng hoàn', 'color' => '#FF802C'],
            'transfer' => ['name' => 'Chuyển nội bộ', 'color' => '#32D593'],
        ];
        return (!empty($this->in_type) && isset($list[$this->in_type])) ? $list[$this->in_type] : '';
    }

    public function getOutTypesAttribute() {
        $list = [
            'sale' => ['name' => 'Bán ra', 'color' => '#3986FF'],
            'destroy'  => ['name' => 'Xuất hủy', 'color' => '#172228'],
            'return'   => ['name' => 'Hoàn đổi', 'color' => '#32D593'],
            'transfer' => ['name' => 'Chuyển kho', 'color' => '#FF802C'],
            'donate' => ['name' => 'Xuất tặng', 'color' => '#FF4A65'],
            'use' => ['name' => 'Xuất sử dụng', 'color' => '#29C6E3'],
        ];
        return (!empty($this->out_type) && isset($list[$this->out_type])) ? $list[$this->out_type] : '';
    }

    // 'ready', 'picking', 'delivering', 'return', 'returned', 'delivered', 'cancel'
    public function getShippingStatussAttribute() {
        $list = [
            'ready'      => ['name' => 'Chờ lấy hàng', 'color' => '#677788'],
            'picking'    => ['name' => 'Đang lấy hàng', 'color' => '#3986FF'],
            'delivering' => ['name' => 'Đang giao hàng', 'color' => '#FF802C'],
            'return'     => ['name' => 'Chuyển hoàn', 'color' => '#3986FF'],
            'returned'   => ['name' => 'Đã chuyển hoàn', 'color' => '#3986FF'],
            'delivered'  => ['name' => 'Đã giao hàng', 'color' => '#32D593'],
            'cancel'     => ['name' => 'Hủy', 'color' => '#172228']
        ];
        return (!empty($this->shipping_status) && isset($list[$this->shipping_status])) ? $list[$this->shipping_status] : '';
    }

    public function getInvoiceAttribute() {
        $output = null;
        if ($this->platform === 'website') $output = Order::where('id', $this->invoice_id)->first();
        return $output;
    }

    /**
     * Relationship
     */

    public function ticket() {
        return $this->hasOne('\Modules\Stock\Entities\Ticket', 'request_id', 'id')->with('usr');
    }

    public function products() {
        return $this->hasMany('\Modules\Stock\Entities\RequestProduct', 'request_id', 'id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'sto__request_products.product_id')
            ->select(['sto__request_products.*', 'idx', 'p.name', 'p.unit', 'p.weight', 'p.image']);
    }

    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }

    public function in_stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'in_stock_id', 'id');
    }

    public function out_stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'out_stock_id', 'id');
    }

    public function out_customer() {
        return $this->belongsTo('\Modules\Customer\Entities\Customer', 'out_customer_id', 'id')->withTrashed();
    }

    public function usr() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id');
    }

    public function manager() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'st_manager_id', 'id');
    }

    public function storekeeper() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'storekeeper_id', 'id');
    }

    public function reviewer() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'reviewer_id', 'id');
    }

    public function owner() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'owner_id', 'id');//->withTrashed();
    }

    /*public function in_supplier() {
        return $this->belongsTo('\Modules\Product\Entities\Supplier', 'in_supplier_id', 'id')->withTrashed();
    }*/

}
