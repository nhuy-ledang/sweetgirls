<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Webhook extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'order__webhooks';

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'ip', 'data'];

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
        'id'   => 'integer',
        'data' => 'json',
    ];
}
