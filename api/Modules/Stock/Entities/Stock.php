<?php namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;
use Imagy;
use Modules\Location\Entities\District;
use Modules\Location\Entities\Province;
use Modules\Location\Entities\Ward;

/***
 * Class Stock
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Stock extends CoreModel {
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__stocks';

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
    protected $fillable = ['idx', 'name', 'description', 'image', 'type_id', 'phone_number', 'province_id', 'district_id', 'ward_id', 'address', 'st_manager_id', 'default_place'];

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
    protected $appends = ['raw_url', 'thumb_url', 'small_url', 'types', 'province_name', 'district_name', 'ward_name', 'default_places', 'keeper_ids', 'seller_ids'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'type_id'       => 'integer',
        'province_id'   => 'interger',
        'district_id'   => 'interger',
        'ward_id'       => 'interger',
        'st_manager_id' => 'interger',
    ];

    public function getRawUrlAttribute() {
        return media_url_file($this->image);
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getSmallUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'small'));
    }

    public function getTypesAttribute() {
        $output = '';
        if ($this->type_id) {
            $output = Type::where('id', $this->type_id)->first();
        }
        return $output;
    }

    public function getDefaultPlacesAttribute() {
        $list_places = '';
        if ($this->default_place) {
            $list_places = Province::whereIn('id', explode(',', $this->default_place))->get();
        }
        return $list_places;
    }

    public function getProvinceNameAttribute() {
        $output = '';
        if ($this->province_id) {
            $output = Province::where('id', $this->province_id)->first();
        }
        return $output ? $output['name'] : '';
    }

    public function getDistrictNameAttribute() {
        $output = '';
        if ($this->district_id) {
            $output = District::where('id', $this->district_id)->first();
        }
        return $output ? $output['name'] : '';
    }

    public function getWardNameAttribute() {
        $output = '';
        if ($this->ward_id) {
            $output = Ward::where('id', $this->ward_id)->first();
        }
        return $output ? $output['name'] : '';
    }

    public function getKeeperIdsAttribute() {
        $output = StockRole::where('stock_id', $this->id)->where('role', 'keeper')->selectRaw('group_concat(staff_id) as ids')->first();
        return $output && $output['ids'] ? $output['ids'] : '';
    }

    public function getSellerIdsAttribute() {
        $output = StockRole::where('stock_id', $this->id)->where('role', 'seller')->selectRaw('group_concat(staff_id) as ids')->first();
        return $output && $output['ids'] ? $output['ids'] : '';
    }
}
