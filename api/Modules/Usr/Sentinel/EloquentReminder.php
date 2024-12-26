<?php namespace Modules\Usr\Sentinel;

use Modules\Core\Entities\Eloquent\CoreModel;

//use Cartalyst\Sentinel\Reminders\EloquentReminder;

// Refer: Cartalyst\Sentinel\Reminders\EloquentReminder
class EloquentReminder extends CoreModel {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
}
