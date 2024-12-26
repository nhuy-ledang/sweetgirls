<?php

namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ReminderSMS extends CoreModel {
    /**
     * {@inheritDoc}
     */
    protected $table = 'reminder__sms';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'completed',
        'completed_at',
        'data'
    ];

    /**
     * Get mutator for the "completed" attribute.
     *
     * @param  mixed $completed
     * @return bool
     */
    public function getCompletedAttribute($completed) {
        return (bool)$completed;
    }

    /**
     * Set mutator for the "completed" attribute.
     *
     * @param  mixed $completed
     * @return void
     */
    public function setCompletedAttribute($completed) {
        $this->attributes['completed'] = (int)(bool)$completed;
    }

    public function setDataAttribute($value) {
        if (is_array($value)) {
            $this->attributes['data'] = json_encode($value, true);
        } else {
            $this->attributes['data'] = $value;
        }
    }

    public function getDataAttribute($value) {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->attributes['code'];
    }

}