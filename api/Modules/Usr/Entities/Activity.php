<?php namespace Modules\Usr\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Activity extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__activities';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['usr_id', 'content', 'data'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
        'usr_id' => 'integer',
        'data'   => 'json',
    ];

    public function usr() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id')->withTrashed();
    }
}
