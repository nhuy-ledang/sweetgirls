<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/**
 * Class Password
 * @package Modules\User\Entities
 * @author Huy D <huydang1920@gmail.com>
 */
class Password extends CoreModel {
    protected $table = 'user__passwords';

    protected $fillable = [
        'user_id',
        'password',
    ];

    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
    ];

    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User', 'user_id');
    }
}
