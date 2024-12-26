<?php namespace Modules\Core\Traits;

/**
 * Trait CommonTrait
 *
 * @package Modules\Core\Traits
 */
trait CommonTrait {
    public function isValidDate($dateString) {
        return (boolean)strtotime($dateString);
    }

    /**
     * @param $datetime
     *
     * @return mixed
     */
    protected function parseToDateTime($datetime) {
        if (!empty($datetime)) {
            if ($datetime == '0000-00-00 00:00:00') {
                return null;
            }

            return date(DATE_ISO8601, strtotime($datetime));
        }

        return $datetime;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function parseToRespond($data = array()) {
        $r = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $r[$key] = $this->parseToRespond($value);
            } else {
                if (strpos($key, "__") > 0) {
                    $t = explode("__", $key);
                    if (!isset($r[$t[0]])) {
                        $r[$t[0]] = [];
                    }
                    $r[$t[0]][$t[1]] = $value;
                } else {
                    $r[$key] = $value;
                }
            }
        }

        return $r;
    }

    /**
     * @param string $q
     *
     * @return array
     */
    public function parseToArray($q = '') {
        $output = [];

        $q = utf8_strtolower($q);
        //$q = mb_strtolower($q);
        //$q = strtolower($q);
        //$q = trim(preg_replace('/\s+/', ' ', $q));
        $q = explode(' ', $q);

        foreach ($q as $v) {
            $v = trim($v);
            if (empty($v)) continue;
            $output[] = $v;
        }

        return $output;
    }

    /***
     * Generate query for datetime search
     *
     * @param string $fieldName
     * @param string $localTime : Datetime
     * @param integer $timezoneOffset : Minute
     *
     * @return array
     */
    public function getWhereRawByLocalTime($fieldName = 'created_at', $localTime, $timezoneOffset = 0) {
        // Convert Local Date to UTC
        if ($timezoneOffset) {
            $serverTime = strtotime($localTime) + $timezoneOffset * 60;
        } else {
            $serverTime = strtotime($localTime);
        }
        $timeStart = date('Y-m-d H:i:s', $serverTime);
        $timeEnd = date('Y-m-d H:i:s', $serverTime + 86400 - 1);

        return ["(UNIX_TIMESTAMP({$fieldName}) >= UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP({$fieldName}) < UNIX_TIMESTAMP(?))", ["$timeStart", "$timeEnd"]];
    }

    /**
     * Get Local Date Form Timezone
     * @param int $timezoneOffset
     * @param string $format
     * @return false|string
     */
    public function getDateLocalFromTz($timezoneOffset = 0, $format = 'Y-m-d H:i:s') {
        /*$serverTime = time() - (int)$timezoneOffset * 60;

        return date($format, $serverTime);*/
        return date($format);
    }

    /**
     * Convert Local Date to UTC
     * @param $localTime
     * @param int $timezoneOffset
     * @param string $format
     * @return false|string
     */
    public function getDateLocalToUTC($localTime, $timezoneOffset = 0, $format = 'Y-m-d H:i:s') {
        $serverTime = strtotime($localTime) + $timezoneOffset * 60;

        return date($format, $serverTime);
    }

    /** Write log request params **/
    protected function writeRequestLog() {
        try {
            //$input = json_decode(file_get_contents("php://input"), true);
            $input = $this->request->all();
            $filepath = storage_path('app/request.log');

            // Create file log
            if (!file_exists($filepath)) {
                $handle = fopen($filepath, "w+");
                fclose($handle);
                chmod($filepath, 0777);
            }

            $limit = 100;
            $handle = fopen($filepath, "r");
            if ($handle && $input) {
                $data = array();
                // Prepend line
                $data[0] = json_encode($input) . "\n";

                $linecount = 0;
                while (($line = fgets($handle)) !== false) {
                    $linecount++;
                    $data[$linecount] = $line;
                    if ($linecount >= $limit - 1) {
                        break;
                    }
                }
                fclose($handle);

                // Empty file
                $handle = fopen($filepath, 'w+');
                fclose($handle);

                // Write data
                $handle = fopen($filepath, 'a+');
                foreach ($data as $line) {
                    fwrite($handle, $line);
                }
                fclose($handle);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Generate Random String
     *
     * @param int $length
     * @param bool $is_caps
     * @param bool $is_number
     * @return string
     */
    protected function generateRandomString($length = 10, $is_caps = true, $is_number = true) {
        return str_random_alpha_numeric($length, $is_caps, $is_number);
    }

    /**
     * Converting timestamp to time ago in PHP e.g 1 day ago, 2 days ago…
     * Use example
     * echo fromNow('2013-05-01 00:22:35');
     * echo fromNow('@1367367755'); # timestamp input
     * echo fromNow('2013-05-01 00:22:35', true);
     * Output
     * 4 months ago
     * 4 months, 2 weeks, 3 days, 1 hour, 49 minutes, 15 seconds ago
     *
     * @param $datetime
     * @param bool $full
     *
     * @return string
     * @throws \Exception
     */
    public function fromNow($datetime, $full = false) {
        return from_now($datetime, $full);
    }
}
