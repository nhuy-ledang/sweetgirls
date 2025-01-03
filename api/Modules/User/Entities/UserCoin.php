<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/**
 * Class UserCoin
 * @package Modules\User\Entities
 */
class UserCoin extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__coins';

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
    protected $fillable = ['user_id', 'type', 'obj_id', 'coins', 'total'];

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
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
        'obj_id'  => 'integer',
        'coins'   => 'integer',
        'total'   => 'integer',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User');
    }

    /*public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order');
    }*/
}
