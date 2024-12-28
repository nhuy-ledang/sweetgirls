<?php

namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Setting extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting';

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
    protected $fillable = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'serialized' => 'boolean',
    ];

    public function getValueAttribute($value) {
        if ($this->serialized) {
            return json_decode($value, true);
        }

//        if (is_numeric($value)) {
//            return (float)$value;
//        }

        return $value;
    }
}
