<?php namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Ticket
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Ticket extends CoreModel {
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__tickets';

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
    protected $fillable = ['idx', 'request_id', 'stock_id', 'owner_id', 'type', 'status', 'note', 'reviewer_id', 'approved_at', 'rejected_at'];

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
    protected $appends = ['status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'request_id'  => 'integer',
        'stock_id'    => 'integer',
        'owner_id'    => 'integer',
        'reviewer_id' => 'integer',
        'status'      => 'integer',
    ];

    public function getStatusNameAttribute() {
        $list = ['new' => 'Phiếu tạm', 'completed' => 'Hoàn thành', 'rejected' => 'Hủy bỏ'];
        return (!empty($this->status) && isset($list[$this->status])) ? $list[$this->status] : '';
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->status)) $hidden = array_merge($hidden, ['status_name']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */

    public function request() {
        return $this->belongsTo('\Modules\Stock\Entities\Request', 'request_id', 'id')->with('stock', 'products', 'ticket', 'owner', 'storekeeper');
    }

    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }

    public function products() {
        return $this->hasMany('\Modules\Stock\Entities\StoProduct', 'ticket_id', 'id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'sto__products.product_id')
            ->select(['sto__products.*', 'idx', 'p.name', 'p.unit', 'p.weight', 'p.image']);
    }

    public function invoice() {
        return $this->belongsTo('\Modules\Order\Entities\Invoice', 'invoice_id', 'id')->withTrashed();
    }

    /*public function owner() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'owner_id', 'id')->withTrashed()
            ->leftJoin('usrs as st', 'st.usr_id', 'usrs.id')->select(['usrs.id', 'usrs.first_name', 'usrs.last_name', 'usrs.avatar', 'usrs.avatar_url', 'st.fullname', 'st.phone_number', 'st.email', 'st.position']);
    }*/
    public function usr() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id');
    }

    public function att_files() {
        return $this->hasMany('\Modules\Stock\Entities\TicketFile', 'ticket_id', 'id')->where('type', 'att');
    }

    public function cert_files() {
        return $this->hasMany('\Modules\Stock\Entities\TicketFile', 'ticket_id', 'id')->where('type', 'cert');
    }
}
