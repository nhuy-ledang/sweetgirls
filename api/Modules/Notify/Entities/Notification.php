<?php

namespace Modules\Notify\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Notification extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notify__notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'object_id',
        'title',
        'message',
        'type',
        'data',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'  => 'integer',
        'is_read'  => 'boolean',
    ];

    public function setDataAttribute($value) {
        if (is_array($value)) {
            $this->attributes['data'] = json_encode($value, true);
        } else {
            $this->attributes['data'] = $value;
        }
    }

    public function getDataAttribute($value) {
        $output = [];
        try {
            $output = json_decode($value, true);
        } catch (\Exception $ex) {

        }
        return $output;
    }

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
