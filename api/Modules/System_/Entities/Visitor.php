<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Visitor extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys__visitors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'ip', 'session_id', 'clicks'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'user_id'    => 'integer',
        'id'         => 'integer',
        'clicks'     => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
