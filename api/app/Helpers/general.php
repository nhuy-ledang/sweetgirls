<?php
/**

 */

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }
}

/**
 * @param string $key
 * @return array|\Illuminate\Http\Request|mixed|null|string
 */
function requestValue($key = '') {
    if (!$key) return '';
    $value = request()->header($key);
    if (!$value) $value = request($key);
    if (!$value) {
        $headers = getallheaders();
        if (isset($headers[$key])) $value = $headers[$key];
    }
    if (!$value) $value = request()->cookie($key);

    return $value;
}

/**
 * @return string
 */
function detect_platform() {
    //Detect App
    $platform = request('device_platform');
    if (!$platform) $platform = requestValue('Device-Platform');
    if (!$platform) $platform = requestValue('Platform');
    if ($platform) {
        return strtolower($platform);
    }
    /*//Detect special conditions devices
   if (!isset($_SERVER['HTTP_USER_AGENT'])) {
       return 'unknown';
   } else {
       $iPod = stripos($_SERVER['HTTP_USER_AGENT'], 'iPod');
       $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone');
       $iPad = stripos($_SERVER['HTTP_USER_AGENT'], 'iPad');
       $android = stripos($_SERVER['HTTP_USER_AGENT'], 'Android');
       $webOS = stripos($_SERVER['HTTP_USER_AGENT'], 'webOS');
       $windowPhone = stripos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone');
       //do something with this information
       if ($iPod || $iPhone || $iPad) {
           return 'ios';
       } else if ($android) {
           return 'android';
       } else if ($webOS || $windowPhone) {
           return 'unknown';
       }
   }*/
    return 'web';
}

/**
 * @return string
 */
function detect_token() {
    $env = request('device_token');
    if (!$env) $env = requestValue('Device-Token');
    return $env;
}

/**
 * @return string
 */
function detect_env() {
    $env = request('env');
    if (!$env) $env = requestValue('App-Env');
    return $env;
}

/**
 * @return string manager, social, student, student_nqh
 */
function app_env() {
    $env = trim(strtolower(requestValue('ENV')));
    $drivers = config('appsystems.drivers');
    return in_array($env, $drivers) ? $env : $drivers[0];
}

/**
 * @return string
 */
function app_name() {
    return app_env() . '_' . trim(strtolower(detect_platform()));
}

function sms_generate_code_number($length = 5) {
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= mt_rand(0, 9);
    }
    return $str;
}

/**
 * Get Date Iso 8601
 *
 * @param $datetime
 * @return mixed
 */
function date_iso8601($datetime) {
    if (is_string($datetime) && $datetime) {
        if ($datetime == '0000-00-00 00:00:00') {
            return null;
        }

        return date(DATE_ISO8601, strtotime($datetime));
    }

    return $datetime;
}

/**
 * Convert date iso to db
 *
 * @param $datetime
 * @return false|string
 */
function date_iso8601_to_db($datetime) {
    return date('Y-m-d H:i:s', strtotime($datetime));
}

/**
 * Generate Random String
 *
 * @param int $length
 * @param bool $is_caps
 * @param bool $is_number
 * @return string
 */
function str_random_alpha_numeric($length = 16, $is_caps = true, $is_number = true) {
    $caps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nums = '0123456789';
    $characters = "abcdefghijklmnopqrstuvwxyz" . ($is_caps ? $caps : '') . ($is_number ? $nums : '');
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * Generate Random String
 *
 * @param int $length
 * @return string
 */
function str_random_numeric($length = 9) {
    $characters = "0123456789";
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * Generate a more truly "random" alpha-numeric string.
 *
 * @param int $length
 * @return string
 * @throws \RuntimeException
 */
function str_random_not_cap($length = 16) {
    return str_random_alpha_numeric($length, false, true);
}

/**
 * Html Decode
 *
 * @param $value
 * @return string
 */
function html_decode_helper($value) {
    return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Decode the given float.
 *
 * @param  mixed  $value
 * @return mixed
 */
function fromFloat($value) {
    switch ((string)$value) {
        case 'Infinity':
            return INF;
        case '-Infinity':
            return -INF;
        case 'NaN':
            return NAN;
        default:
            return (float)$value;
    }
}

/**
 * Cast an attribute to a native PHP type.
 * @param $value
 * @param $type
 * @return bool|mixed
 */
function cast_helper($value, $type) {
    if (is_null($value)) return $value;

    switch (trim(strtolower($type))) {
        case 'int':
        case 'integer':
            return (int)$value;
        case 'real':
        case 'float':
        case 'double':
            return fromFloat($value);
        case 'decimal':
            return number_format($value, 0, '.', '');
        case 'string':
            return (string)$value;
        case 'bool':
        case 'boolean':
            return (bool)$value;
        case 'object':
            return json_decode($value, false);
        case 'array':
        case 'json':
            return json_decode($value, true);
        default:
            return $value;
    }
}

/**
 * Transform Helper
 * @param array $input
 * @param array $casts
 * @return array
 */
function transform_helper($input = [], $casts = []) {
    $newItem = [];
    foreach ($input as $k => $v) $newItem[$k] = isset($casts[$k]) ? cast_helper($v, $casts[$k]) : $v;

    return $newItem;
}

/**
 * Number Phone
 *
 * @param $phone_number
 * @param string $calling_code
 * @return null|string
 */
function phone_number_helper($phone_number, $calling_code = '84') {
    if ($phone_number && strlen($phone_number) >= 8) {
        return $calling_code . ltrim(ltrim(ltrim(str_replace([' ', '.', '-'], '', $phone_number), '0'), '+'), $calling_code);
    } else {
        return null;
    }
}

/**
 * Get Calling Code and Phone Number.
 *
 * @param string $phone_number_input
 * @return array
 */
function calling2phone($phone_number_input) {
    if (!$phone_number_input) return [null, null];
    $phone_number_input = '+' . ltrim(trim(str_replace([' ', '.', '-'], '', (string)$phone_number_input)), '+');

    $list = ['+998', '+996', '+995', '+994', '+993', '+992', '+977', '+976', '+975', '+974', '+973', '+972', '+971', '+970', '+968', '+967', '+966', '+965', '+964', '+963', '+962', '+961', '+960', '+886', '+880', '+856', '+855', '+853', '+852', '+850', '+692', '+691', '+690', '+689', '+688', '+687', '+686', '+685', '+683', '+682', '+681', '+680', '+679', '+678', '+677', '+676', '+675', '+674', '+673', '+672', '+670', '+599', '+598', '+597', '+596', '+595', '+594', '+593', '+592', '+591', '+590', '+509', '+508', '+507', '+506', '+505', '+504', '+503', '+502', '+501', '+500', '+423', '+421', '+420', '+389', '+387', '+386', '+385', '+383', '+382', '+381', '+380', '+378', '+377', '+376', '+375', '+374', '+373', '+372', '+371', '+370', '+359', '+358', '+357', '+356', '+355', '+354', '+353', '+352', '+351', '+350', '+299', '+298', '+297', '+291', '+290', '+269', '+268', '+267', '+266', '+265', '+264', '+263', '+262', '+261', '+260', '+258', '+257', '+256', '+255', '+254', '+253', '+252', '+251', '+250', '+249', '+248', '+247', '+246', '+245', '+244', '+243', '+242', '+241', '+240', '+239', '+238', '+237', '+236', '+235', '+234', '+233', '+232', '+231', '+230', '+229', '+228', '+227', '+226', '+225', '+224', '+223', '+222', '+221', '+220', '+218', '+216', '+213', '+212', '+211', '+98', '+95', '+94', '+93', '+92', '+91', '+90', '+86', '+84', '+82', '+81', '+66', '+65', '+64', '+63', '+62', '+61', '+60', '+58', '+57', '+56', '+55', '+54', '+53', '+52', '+51', '+49', '+48', '+47', '+46', '+45', '+44', '+43', '+41', '+40', '+39', '+36', '+34', '+33', '+32', '+31', '+30', '+27', '+20', '+7', '+1'];

    $calling_code = null;
    $phone_number = null;
    foreach ($list as $code) {
        if (strpos($phone_number_input, $code) === 0) {
            $calling_code = $code;
            $temp = substr($phone_number_input, strlen($code));
            $phone_number = $temp ? $temp : null;
        }
    }
    if ($calling_code && $phone_number) {
        if ($calling_code == '+84') {
            $phone_number = ltrim($phone_number, '0');
            if (9 <= strlen($phone_number) && strlen($phone_number) <= 11) {
                $phone_number = '0' . $phone_number;
            } else {
                $phone_number = null;
            }
        }
    } else { // Default is Vietnam
        $calling_code = '+84';
        $phone_number = ltrim(ltrim($phone_number_input, '+'), '0');
        if (9 <= strlen($phone_number) && strlen($phone_number) <= 11) {
            $phone_number = '0' . $phone_number;
        } else {
            $phone_number = null;
        }
    }

    return [ltrim($calling_code, '+'), $phone_number];
}

/**
 * Get Local Phone Number.
 *
 * @param $phone_number_input
 * @return string
 */
function phone2local($phone_number_input) {
    if (!$phone_number_input) return null;
    list($calling_code, $phone_number) = calling2phone($phone_number_input);
    if ($calling_code && $phone_number) {
        if ($calling_code == '84') {
            return $phone_number;
        } else {
            return '+' . $calling_code . $phone_number;
        }
    }
    return '';
}

/**
 * @param array $data
 * @return array
 */
function query_to_object($data = []) {
    $r = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $r[$key] = query_to_object($value);
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
 * Generate select raw fof table
 *
 * @param string $tableName
 * @param string $shortName
 * @param array $properties
 * @return string
 */
function generate_select_raw($tableName, $shortName, $properties = []) {
    $r = [];
    foreach ($properties as $value) {
        $r[] = "`$tableName`.`$value` AS " . $shortName . "__$value";
    }

    return implode(',', $r);
}

/**
 * Calc Distance
 *
 * @param $lat
 * @param $long
 * @param $lat2
 * @param $long2
 * @return float|int
 */
function calc_distance($lat, $long, $lat2, $long2) {
    if (($lat == 0 && $long == 0) || $lat2 == 0 && $lat2 == 0) {
        return null;
    }
    $theta = $long - $long2;
    $distance = (sin(deg2rad($lat)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $distance = acos($distance);
    $distance = rad2deg($distance);
    $distance = $distance * 60 * 1.1515 * 1.609344;

    return (round($distance, 2));
}

function utf8_to_ascii($str) {
    if (!$str) return false;
    $unicode = [
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'D' => 'Đ',
        'd' => 'đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ''  => '́|̉|̀|̣|̃',
    ];
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    return $str;
}

function trim_space_all($str) {
    return preg_replace('/[ ]/', '', $str);
}

function remove_multi_space($str) {
    return preg_replace('/\s\s+/', ' ', $str);
}

function to_alias($str) {
    $ascii = trim(strtolower(utf8_to_ascii($str)));
    $ascii = strtolower(preg_replace('([^a-zA-Z0-9])', ' ', $ascii));
    $ascii = preg_replace('/\s\s+/', ' ', $ascii);
    $ascii = preg_replace('/[ ]/', '-', $ascii);
    $ascii = preg_replace('{-+}', '-', $ascii);

    return trim(trim($ascii, '-'));
}

function to_ascii($str) {
    return str_replace('-', ' ', to_alias($str));
}

function seo_url($str) {
    $old = substr(trim($str), -5, 5) === '.html';
    if ($old) $str = substr($str, 0, strlen($str) - 5);
    $str = str_replace('&amp;', ' ', $str);
    $str = to_alias($str);

    return $old ? ($str . '.html') : $str;
}

function to_idx($str) {
    $ascii = trim(strtolower(utf8_to_ascii($str)));
    $ascii = preg_replace('/\s\s+/', ' ', $ascii);
    $ascii = preg_replace('/[ ]/', '', $ascii);

    return strtoupper(trim($ascii));
}

/**
 * Fetch YouTube ID from URLs
 *
 * @param string $url
 * @return string
 */
function youtube_id($url) {
    return preg_replace('/[^\w\-\_].*$/', '', preg_replace('/(^.*\/embed\/)|(^.*(\?|\&)v\=)/', '', $url));
}

/**
 * Fetch YouTube ID from URLs
 *
 * @param string $url
 * @return string
 */
function fetch_youtube_id($url) {
    $regex = '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i';
    return preg_replace($regex, '$1', $url);
}

/**
 * Get number of days between two dates
 *
 * @param $date1
 * @param $date2
 * @return float
 */
function number_of_days($date1, $date2) {
    $date1_ts = strtotime($date1);
    $date2_ts = strtotime($date2);
    $diff = $date2_ts - $date1_ts;
    return round($diff / 86400);
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
 * @return string
 * @throws Exception
 */
function from_now($datetime, $full = false) {
    $now = new \DateTime();
    $ago = new \DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'năm',  //'year',
        'm' => 'tháng',//'month',
        'w' => 'tuần', //'week',
        'd' => 'ngày', //'day',
        'h' => 'giờ',  //'hour',
        'i' => 'phút', //'minute',
        's' => 'giây', //'second',
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);

    return $string ? implode(', ', $string) . ' trước' : 'mới đây';//' ago' : 'just now';
}

/**
 * Number Pad
 *
 * @param $val
 * @param int $len
 * @return string
 */
function number_pad($val, $len = 2) {
    $val = (string)$val;
    while (strlen($val) < $len) {
        $val = "0" . $val;
    }
    return $val;
}

/**
 * Converts an integer into the alphabet base (A-Z).
 * @param $n
 * @return string
 */
function num2alpha($n) {
    for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n % 26 + 0x41) . $r;
    return $r;
}

function number_format_vnd($number) {
    if (!is_numeric($number)) return false;

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        //trigger_error('number_format_vnd only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
        return false;
    }

    if ($number < 0) {
        return false;
    }

    $hyphen = ' ';
    $conjunction = ' ';
    $separator = ' ';
    $dictionary = [
        0          => 'không',
        1          => 'một',
        2          => 'hai',
        3          => 'ba',
        4          => 'bốn',
        5          => 'năm',
        6          => 'sáu',
        7          => 'bảy',
        8          => 'tám',
        9          => 'chín',
        10         => 'mười',
        20         => 'hai mươi',
        30         => 'ba mươi',
        40         => 'bốn mươi',
        50         => 'năm mươi',
        60         => 'sáu mươi',
        70         => 'bảy mươi',
        80         => 'tám mươi',
        90         => 'chín mươi',
        100        => 'trăm',
    ];

    $string = '';
    if ($number >= 1000000000) {//Tỷ
        $remainder = $number % 1000000000;
        $billion = (int)($number / 1000000000);
        $string .= number_format_vnd($billion) . ' tỷ ' . ($remainder ? number_format_vnd($remainder) : '');
    } else if ($number >= 1000000) {//Triệu
        $remainder = $number % 1000000;
        $million = (int)($number / 1000000);
        $string .= number_format_vnd($million) . ' triệu ' . ($remainder ? number_format_vnd($remainder) : '');
    } else if ($number >= 1000) {//Nghìn
        $remainder = $number % 1000;
        $thousands = (int)($number / 1000);
        $string .= number_format_vnd($thousands) . ' nghìn ' . ($remainder ? number_format_vnd($remainder) : '');
    }
    // Ascending
    else if ($number < 11) {
        $string = $dictionary[$number];
    } else if ($number < 100) {
        $tens = ((int)($number / 10)) * 10;
        $units = $number % 10;
        $string = $dictionary[$tens];
        if ($units) {
            $string .= $hyphen . $dictionary[$units];
        }
    } else if ($number < 1000) {
        $hundreds = $number / 100;
        $remainder = $number % 100;
        $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
        if ($remainder) {
            $string .= $conjunction . number_format_vnd($remainder);
        }
    }
    return $string;
}

function number_format_words($number) {
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = [
        0        => 'zero',
        1        => 'one',
        2        => 'two',
        3        => 'three',
        4        => 'four',
        5        => 'five',
        6        => 'six',
        7        => 'seven',
        8        => 'eight',
        9        => 'nine',
        10       => 'ten',
        11       => 'eleven',
        12       => 'twelve',
        13       => 'thirteen',
        14       => 'fourteen',
        15       => 'fifteen',
        16       => 'sixteen',
        17       => 'seventeen',
        18       => 'eighteen',
        19       => 'nineteen',
        20       => 'twenty',
        30       => 'thirty',
        40       => 'fourty',
        50       => 'fifty',
        60       => 'sixty',
        70       => 'seventy',
        80       => 'eighty',
        90       => 'ninety',
        100      => 'hundred',
        1000     => 'thousand',
        100000   => 'lakh',
        10000000 => 'crore'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        //trigger_error('number_format_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
        return false;
    }

    if ($number < 0) {
        return $negative . number_format_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . number_format_words($remainder);
            }
            break;
        case $number < 100000:
            $thousands = ((int)($number / 1000));
            $remainder = $number % 1000;

            $thousands = number_format_words($thousands);

            $string .= $thousands . ' ' . $dictionary[1000];
            if ($remainder) {
                $string .= $separator . number_format_words($remainder);
            }
            break;
        case $number < 10000000:
            $lakhs = ((int)($number / 100000));
            $remainder = $number % 100000;

            $lakhs = number_format_words($lakhs);

            $string = $lakhs . ' ' . $dictionary[100000];
            if ($remainder) {
                $string .= $separator . number_format_words($remainder);
            }
            break;
        case $number < 1000000000:
            $crores = ((int)($number / 10000000));
            $remainder = $number % 10000000;

            $crores = number_format_words($crores);

            $string = $crores . ' ' . $dictionary[10000000];
            if ($remainder) {
                $string .= $separator . number_format_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = number_format_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= number_format_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return $string;
}

if (!function_exists('str_slug')) {
    function str_slug($title) {
        //return \Modules\Media\Helpers\FileHelper::slug($title);
        return \Illuminate\Support\Str::slug($title);
    }
}
