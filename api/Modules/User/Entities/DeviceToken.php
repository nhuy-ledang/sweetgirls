<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class DeviceToken extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'user__device_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'app',
        'device_platform',
        'device_token',
    ];

    public function user() {
        return $this->belongsTo(\Sentinel::getUserRepository()->getModel());
    }
}
