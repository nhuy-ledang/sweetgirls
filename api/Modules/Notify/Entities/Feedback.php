<?php

namespace Modules\Notify\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Feedback extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notify__feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'email',
        'category',
        'know_from',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }
}
