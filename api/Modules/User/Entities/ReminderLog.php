<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ReminderLog extends CoreModel {
    protected $table = 'reminder__logs';
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'data',
    ];

    public function setDataAttribute($value) {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->attributes['data'] = json_encode($value, true);
    }

    public function getDataAttribute($value) {
        return json_decode($value, true);
    }
}
