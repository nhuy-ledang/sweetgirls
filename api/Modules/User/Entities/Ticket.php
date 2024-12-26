<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Ticket extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__tickets';

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
    protected $fillable = ['user_id', 'order_ticket_id', 'type', 'ticket_id', 'name', 'code', 'activated_at', 'date', 'location', 'start_time', 'end_time'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'user_id'         => 'integer',
        'order_ticket_id' => 'integer',
        'ticket_id'       => 'integer',
        'price'           => 'double',
    ];

    /**
     * Relationship
     */

    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User');
    }

    public function activity_order_ticket() {
        return $this->belongsTo('\Modules\Activity\Entities\OrderTicket', 'order_ticket_id', 'id')->whete('type', 'activity');
    }

    public function exhibit_order_ticket() {
        return $this->belongsTo('\Modules\Exhibit\Entities\OrderTicket', 'order_ticket_id', 'id')->whete('type', 'exhibit');
    }

    public function getOrderTicket() {
        if ($this->type == 'activity') {
            return $this->activity_order_ticket;
        } else {
            return $this->exhibit_order_ticket;
        }
    }
}
