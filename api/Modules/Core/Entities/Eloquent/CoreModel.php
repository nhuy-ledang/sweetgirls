<?php namespace Modules\Core\Entities\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\Traits\CoreModelTrait;

class CoreModel extends Model {
    use CoreModelTrait;

    /**
     * Locale
     *
     * @var string
     */
    protected $locale = 'vi';

    private static $cacheData = array();

    public function __construct(array $attributes = []) {
        $this->locale = request()->getLocale();

        parent::__construct($attributes);
    }

    public static function getCache($key) {
        return (isset(self::$cacheData[$key]) ? self::$cacheData[$key] : null);
    }

    public static function setCache($key, $value) {
        self::$cacheData[$key] = $value;
    }

    public static function hasCache($key) {
        return isset(self::$cacheData[$key]);
    }

    public function getCreatedAtAttribute($value) {
       return $this->convertToTimezone($value);
    }

    public function getUpdatedAtAttribute($value) {
        return $this->convertToTimezone($value);
    }
}
