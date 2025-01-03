<?php namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;
use Modules\Order\Traits\OrderStatusTrait;

/***
 * Class Order
 *
 * @package Modules\Order\Entities

 
 */
class Order extends CoreModel {
    use SoftDeletes;
    use OrderStatusTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

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
    protected $fillable = ['idx', 'master_id', 'invoice_no', 'invoice_prefix', 'user_id', 'user_group_id', 'usr_id', 'pusr_id', 'first_name', 'last_name', 'gender', 'email', 'phone_number', 'telephone', 'fax', 'is_invoice', 'company', 'company_tax', 'company_email', 'company_address', 'payment_method', 'payment_code',
        'coins', 'sub_total', 'vat', 'total', 'total_coins',
        'note', 'comment', 'tags', 'status', 'order_status', 'payment_status', 'sto_request_id', 'reason', 'affiliate_id', 'tracking', 'lang', 'currency_code', 'summary', 'transaction_no', 'response_code', 'payload', 'ip', 'referral_code', 'forwarded_ip', 'user_agent', 'accept_language', 'payment_at'];

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
    protected $appends = ['no', 'display', 'order_status_name', 'payment_status_name', 'shipping_status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'master_id'         => 'integer',
        'invoice_no'        => 'integer',
        'user_id'           => 'integer',
        'user_group_id'     => 'integer',
        'usr_id'            => 'integer',
        'pusr_id'           => 'integer',
        'is_invoice'        => 'boolean',
        'coins'             => 'integer',
        'sub_total'         => 'double',
        'discount_total'    => 'double',
        'voucher_total'     => 'double',
        'shipping_fee'      => 'double',
        'shipping_discount' => 'double',
        'vat'               => 'double',
        'total'             => 'double',
        'total_coins'       => 'integer',
        'affiliate_id'      => 'integer',
        'sto_request_id'    => 'integer',
    ];

    public function getNoAttribute() {
        //return $this->invoice_prefix . number_pad($this->invoice_no, 4);
        return $this->idx;
    }

    public function getDisplayAttribute() {
        $display = trim(($this->last_name ? $this->last_name : '') . ' ' . $this->first_name);
        if ($display) {
            return $display;
        } else {
            return '';
        }
    }

    public function getPaymentAtAttribute($value) {
        return $this->convertToTimezone($value);
    }

    /*public function getPaymentMethodAttribute() {
        $list = [
            'domestic'      => Lang::get('transaction.payment_method.domestic'),
            'international' => Lang::get('transaction.payment_method.international'),
            'qr'            => Lang::get('transaction.payment_method.qr'),
            'cash'          => Lang::get('transaction.payment_method.cash'),
            'bank_transfer' => Lang::get('transaction.payment_method.bank_transfer'),
        ];
        return isset($list[$this->payment_code]) ? $list[$this->payment_code] : 'N/A';
    }*/

    public function getTagsAttribute() {
        if (is_array($this->attributes['tags'])) {
            return $this->attributes['tags'];
        } else {
            return $this->attributes['tags'] ? explode(',', $this->attributes['tags']) : [];
        }
    }

    /**
     * Handle Data when eloquent return json data from database
     *
     * @return array
     */
    public function toArray() {
        $hidden = [];
        if (is_null($this->invoice_no)) $hidden[] = 'no';
        if (is_null($this->first_name)) $hidden[] = 'display';
        //if (is_null($this->payment_code)) $hidden[] = 'payment_method';
        if (is_null($this->order_status)) $hidden[] = 'order_status_name';
        if (is_null($this->payment_status)) $hidden[] = 'payment_status_name';
        if (is_null($this->shipping_status)) $hidden[] = 'shipping_status_name';
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }

    public function usr() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id');
    }

    public function shipping() {
        return $this->hasOne('\Modules\Order\Entities\OrderShipping');
    }

    public function order_products() {
        return $this->hasMany('\Modules\Order\Entities\OrderProduct')->leftJoin('pd__products as p', 'p.id', 'product_id')
            ->select(['order__products.*', 'p.weight', 'p.image', 'p.alias', 'p.short_description', 'p.name as p__name']);
    }

    /**
     * The Products relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany {
        return $this->belongsToMany('\Modules\Product\Entities\Product', 'order__products', 'order_id', 'product_id');
    }

    public function affiliate() {
        return $this->belongsTo('Modules\Affiliate\Entities\Agent', 'affiliate_id', 'id');
    }
}
