<?php

namespace Modules\Core\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/**
 * Class SeoUrl
 * @package Modules\Core\Entities
 */
class SeoUrl extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seo__url';


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
    protected $fillable = ['lang', 'query', 'keyword', 'push'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /*public static function getUrl($key) {
        if (self::hasCache($key)) {
            return self::getCache($key);
        } else {
            $s = self::whereRaw('`query` = "' . $key . '"')->first();
            if ($s) {
                $value = $s->keyword;
                self::setCache($key, $value);
                return $value;
            } else {
                self::setCache($key, null);
                return null;
            }
        }
    }

    public static function getCategoryUrl($id) {
        return self::getUrl("category_id=$id");
    }

    public static function getProvinceUrl($id) {
        return self::getUrl("province_id=$id");
    }

    public static function getDistrictUrl($id) {
        return self::getUrl("district_id=$id");
    }

    public static function getPlaceUrl($id) {
        return self::getUrl("place_id=$id");
    }

    public static function getCatProvinceUrl($id) {
        return self::getUrl("capro_id=$id");
    }

    public static function getCatDistrictUrl($id) {
        return self::getUrl("cadis_id=$id");
    }

    public static function getProductCategoryUrl($id) {
        return self::getUrl("pcate_id=$id");
    }

    public static function getProductUrl($id) {
        return self::getUrl("product_id=$id");
    }*/
}
