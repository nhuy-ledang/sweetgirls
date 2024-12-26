<?php

namespace Modules\Notify\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Contact extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notify__contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fullname',
        'address',
        'phone_number',
        'email',
        'content'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'  => 'integer',
        'approved' => 'boolean',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
