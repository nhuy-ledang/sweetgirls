<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Session extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crt__sessions';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'shipping_fee'      => 'integer',
        'shipping_discount' => 'integer',
    ];
}
