<?php

namespace Modules\Core\Entities\Traits;

use Carbon\Carbon;
use DateTime;

trait CoreModelTrait {
    protected $timezone_system = 'Asia/Ho_Chi_Minh';
    protected $timezone_client;
    protected $convertTimezone = true;

//    /**
//     * @return mixed
//     */
//    public function getTimezoneClient() {
//        return $this->timezone_client = !is_null($this->timezone_client) ? $this->timezone_client : $this->get_client_timezone();
//    }
//
//    /**
//     * @param mixed $timezone_client
//     */
//    public function setTimezoneClient($timezone_client) {
//        $this->timezone_client = $timezone_client;
//    }

    /**
     * @return string
     */
    public function getTimezoneSystem() {
        return $this->timezone_system = config('config.app.timezone', 'UTC');
    }

    public function canConvertToTimezone($key, $value = true) {
        return !$this->hasSetMutator($key) && in_array($key, $this->getDates()) && DateTime::createFromFormat($this->getDateFormat(), $value) && $this->getTimezoneSystem() != $this->getTimezoneClient() && strtotime($value) > 0;
    }

    /**
     * @param $datetime
     *
     * @return mixed
     */
    public function convertToTimezone($datetime) {
        if (!empty($datetime)) {
            if ($datetime == '0000-00-00 00:00:00') {
                return null;
            }

            return date(DATE_ISO8601, strtotime($datetime));
        }

        return $datetime;
    }

//    //Handle Data when eloquent return json data from database
//    public function toArray() {
//        $attributes = $this->attributesToArray();
//        foreach (($attributes) as $key => $value) {
//            if ($this->canConvertToTimezone($key, $value)) {
//                if (!is_null($value)) {
//                    $format = $this->getDateFormat();
//                    // user timezone
//                    $timezone = new \DateTimeZone($this->getTimezoneSystem());
//                    $carbon = Carbon::createFromFormat($format, $value, $timezone);
//                    // mutates the carbon object immediately
//                    $carbon->setTimezone($this->getTimezoneClient());
//                    // now save to format
//                    $value = $carbon->format($format);
//                    $attributes[$key] = $value;
//                }
//            }
//        }
//        return array_merge($attributes, $this->relationsToArray());
//    }

//    ////Handle Data when eloquent get data from database
//    public function getAttributeValue($key) {
//        $value = parent::getAttributeValue($key);
//
//        if ($this->canConvertToTimezone($key, $value)) {
//            if (!is_null($value)) {
//                $format = $this->getDateFormat();
//                // user timezone
//                $timezone = new \DateTimeZone($this->getTimezoneSystem());
//                $carbon = Carbon::createFromFormat($format, $value, $timezone);
//                // mutates the carbon object immediately
//                $carbon->setTimezone($this->getTimezoneClient());
//                // now save to format
//                $value = $carbon->format($format);
//            }
//        }
//        return $value;
//    }
//
//    /**
//     * Set a given attribute on the model.
//     *
//     * @param  string $key
//     * @param  mixed $value
//     *
//     * @return void
//     */
//    public function setAttribute($key, $value) {
//        if (in_array($key, $this->getDates()) && !$value) {
//            $value = null;
//        }
//        if ($this->canConvertToTimezone($key, $value) && $key != 'created_at' && $key != 'updated_at') {
//            if (!is_null($value)) {
//                $format = $this->getDateFormat();
//                // user timezone
//                $timezone = new \DateTimeZone($this->getTimezoneClient());
//                $carbon = Carbon::createFromFormat($format, $value, $timezone);
//                // mutates the carbon object immediately
//                $carbon->setTimezone($this->getTimezoneSystem());
//                // now save to format
//                $value = $carbon->format($format);
//            }
//            $this->attributes[$key] = $value;
//        } else {
//            parent::setAttribute($key, $value);
//        }
//    }
//
//    public function multiRestore($ids) {
//        $records = $this->onlyTrashed()->whereIn($this->getKeyName(), $ids)->get();
//        foreach ($records as $record) {
//            $record->restore();
//        }
//        return $records->count();
//    }
//
//    public function multiForceDelete($ids) {
//        $records = $this->onlyTrashed()->whereIn($this->getKeyName(), $ids)->get();
//        foreach ($records as $record) {
//            $record->forceDelete();
//        }
//        return $records->count();
//    }
//
//    public function multiDelete($ids) {
//        $records = $this->whereIn($this->getKeyName(), $ids)->get();
//        foreach ($records as $record) {
//            $record->delete();
//        }
//        return $records->count();
//    }
//
//    protected function get_client_timezone() {
//        return config('config.app.timezone', 'UTC');
//    }
}