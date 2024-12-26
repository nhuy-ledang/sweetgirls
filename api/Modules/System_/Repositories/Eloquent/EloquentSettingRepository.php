<?php namespace Modules\System\Repositories\Eloquent;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\System\Entities\Setting;
use Modules\System\Repositories\SettingRepository;

class EloquentSettingRepository extends EloquentBaseRepository implements SettingRepository {
    public static $SETTING_DATA = null;

    /**
     * Transform
     * @param $key
     * @param $value
     * @param $locale
     * @return mixed
     */
    private function transform($key, $value, $locale = 'vi') {
        if (is_array($value)) {
            $keys = [];
            foreach (array_keys($value) as $k) $keys[] = (string)$k;
            if (in_array($locale, $keys)) {
                $newValue = isset($value[$locale]) ? $value[$locale] : '';
                if (!$newValue) {
                    $temps = array_values($value);
                    $newValue = count($temps) ? $temps[0] : '';
                }
                return is_string($newValue) ? html_entity_decode($newValue, ENT_QUOTES, 'UTF-8') : '';
            }
        }

        return is_string($value) ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allRevision() {
        return Cache::remember('settings', $this->cacheExpireMax, function() {
            return $this->model->get();
        });
    }

    /**
     * @param  mixed $data
     * @return object
     */
    public function create($data) {
        Cache::forget('settings');
        return parent::create($data);
    }

    /**
     * @param $model
     * @param  array $data
     * @return object
     */
    public function update($model, $data) {
        Cache::forget('settings');
        return parent::update($model, $data);
    }

    /**
     * @param $model
     * @return bool
     */
    public function destroy($model) {
        Cache::forget('settings');
        return parent::destroy($model);
    }

    /**
     * Get settings
     * @return null|\stdClass
     */
    public function all() {
        if (is_null(self::$SETTING_DATA)) {
            $settings = new \stdClass();

            foreach ($this->allRevision() as $s) {
                $settings->{$s->key} = $s->value;
            }

            self::$SETTING_DATA = $settings;
        }

        return self::$SETTING_DATA;
    }

    /**
     * @return \stdClass
     */
    public function newAll() {
        $settings = new \stdClass();

        foreach ($this->allRevision() as $s) {
            $settings->{$s->key} = $s->value;
        }

        self::$SETTING_DATA = $settings;

        return $settings;
    }

    /**
     * Get setting by key
     * @param $key
     * @param null $default
     * @param string $locale
     * @return null
     */
    public function findByKey($key, $default = null, $locale = 'vi') {
        $settings = $this->all();

        if ($settings && isset($settings->{$key})) {
            return $this->transform($key, $settings->{$key}, $locale);
        } else {
            return $default;
        }
    }

    /**
     * @param $key
     * @return array
     */
    public static function findByKeyAsArray($key) {
        $setting = Setting::where('key', $key)->first();
        if ($setting && is_array($setting->value)) {
            $results = [];
            foreach ($setting->value as $value) {
                $results[$value] = $value;
            }
            return $results;
        } else {
            return [];
        }
    }

    /**
     * @param $key
     * @return string
     */
    public static function findByKeyAsValue($key) {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    }

    /**
     * Create or update setting
     * @param $key
     * @param $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOrUpdate($key, $value) {
        Cache::forget('settings');
        $setting = $this->getModel()->where('key', $key)->first();
        if (!$setting) {
            $setting = $this->getModel();
            $setting->key = $key;
            $keys = explode('_', $key);
            $setting->code = $keys[0];
        }
        if (!is_array($value)) {
            $setting->value = $value;
            $setting->serialized = 0;
        } else {
            $setting->value = json_encode($value, true);
            $setting->serialized = 1;
        }

        $setting->save();

        return $setting;
    }
}
