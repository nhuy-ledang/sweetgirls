<?php namespace Modules\Usr\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Notify extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__notifies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['usr_id', 'object_id', 'title', 'message', 'type', 'data'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'usr_id'  => 'integer',
        'is_read' => 'boolean',
        'data'    => 'json',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id');
    }
}
