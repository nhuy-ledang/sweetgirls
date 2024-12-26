<?php namespace Modules\Staff\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Salary extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'st__salaries';

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['user_id', 'date', 'date_num', 'date_off', 'salary', 'real', 'debt', 'salary_at'];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'user_id'  => 'integer',
        'date_num' => 'integer',
        'date_off' => 'integer',
        'salary'   => 'double',
        'real'     => 'double',
        'debt'     => 'double',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\Staff\Entities\User', 'user_id', 'id');
    }
}
