<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ReminderPhoneNumber extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reminder__phone_numbers';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $fillable = [
        'phone_number',
        'ip',
        'code',
        'completed',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed' => 'bool',
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

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->attributes['code'];
    }

}
