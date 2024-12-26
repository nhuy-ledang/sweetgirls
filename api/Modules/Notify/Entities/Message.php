<?php namespace Modules\Notify\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Message extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notify__messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'to', 'message', 'readed'];

    /**
     * Relationship
     */
    public function fromUser() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'from', 'id')->select(['id', 'email', 'first_name', 'last_name', 'avatar']);
    }

    public function toUser() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'to', 'id')->select(['id', 'email', 'first_name', 'last_name', 'avatar']);
    }
}
