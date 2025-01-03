<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class OptionValue
 *
 * @package Modules\Product\Entities

 
 */
class OptionValue extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__option_values';

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
    protected $fillable = ['option_id', 'name',  'value', 'sort_order'];

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
        'id'         => 'integer',
        'option_id'  => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship
     */
    public function option() {
        return $this->belongsTo('\Modules\Product\Entities\Option');
    }
}
