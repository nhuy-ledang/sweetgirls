<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Feedback extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys__feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'order_id', 'type', 'file', 'phone_number', 'message', 'status'];

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
        'id'         => 'integer',
        'user_id'    => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['file_url'];

    public function getFileUrlAttribute() {
        return media_url_file($this->file);
    }

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
