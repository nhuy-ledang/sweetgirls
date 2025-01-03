<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Flashsale extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__flashsales';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'start_date', 'end_date', 'special_ids', 'status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
        'status' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];
}
