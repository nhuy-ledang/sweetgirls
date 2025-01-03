<?php namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface SettingRepository extends BaseRepository {
    /**
     * Get settings
     * @return null|\stdClass
     */
    public function all();

    /**
     * @return \stdClass
     */
    public function newAll();

    /**
     * Get setting by key
     * @param $key
     * @param null $default
     * @return null
     */
    public function findByKey($key, $default = null);

    /**
     * @param $key
     * @return array
     */
    public static function findByKeyAsArray($key);

    /**
     * @param $key
     * @return string
     */
    public static function findByKeyAsValue($key);

    /**
     * Create or update setting
     * @param $key
     * @param $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOrUpdate($key, $value);
}
