<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Notify extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__notifies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'object_id', 'title', 'message', 'type', 'data'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'data'    => 'json',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
