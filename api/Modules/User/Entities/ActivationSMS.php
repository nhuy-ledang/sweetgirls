<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ActivationSMS extends CoreModel {
    protected $fillable = ['user_id', 'code', 'completed_at', 'completed'];

    protected $table = "activation_sms";

    public function getCompletedAttribute($completed) {
        return (bool)$completed;
    }

    public function setCompletedAttribute($completed) {
        $this->attributes['completed'] = (bool)$completed;
    }


    public function getCode() {
        return $this->attributes['code'];
    }

}