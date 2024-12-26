<?php namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;

class UserBanned extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'user__banned';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'expired_at'
    ];
    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo(\Sentinel::getUserRepository()->getModel(), 'user_id');
    }
}
