<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Address extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__addresses';

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
    protected $fillable = ['user_id', 'first_name', 'last_name', 'phone_number', 'company', 'type', 'address_1', 'address_2', 'city', 'postcode', 'country_id', 'province_id', 'district_id', 'ward_id'];

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
        'id'          => 'integer',
        'user_id'     => 'integer',
        'country_id'  => 'integer',
        'province_id' => 'integer',
        'district_id' => 'integer',
        'ward_id'     => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Relationship
     */

    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User');
    }

    public function country() {
        return $this->belongsTo('Modules\Location\Entities\Country');
    }

    public function province() {
        return $this->belongsTo('Modules\Location\Entities\Province');
    }
}
