<?php
/** HTML decode helper
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
 * @param $phone_number
 * @param string $country_code
 * @return null|string
 */
function phone_number_helper($phone_number, $country_code = '84') {
    if ($phone_number && strlen($phone_number) >= 10) {
        return $country_code . ltrim(ltrim(ltrim(trim(str_replace('.', '', str_replace(' ', '', $phone_number))), '0'), '+'), $country_code);
    } else {
        return null;
    }
}

/**
 * Get thumb (large|thumb|small)
 * @param $path
 * @param string $thumbnail
 * @param boolean $placeholder
 * @return string
 */
function media_url_file($path, $thumbnail = '', $placeholder = false) {
    if ($path == 'NULL') $path = false;
    if (!$path && $placeholder) return $placeholder ? MEDIA_URL . 'placeholder.png' : false;
    $destPath = '/assets/';
    $newDestPath = substr($path, 0, strlen($destPath));
    if ($destPath != $newDestPath) {
        $temps = explode('/', ltrim($path, '/'));
        $destPath = '/' . $temps[0] . '/';
    }
    $extension = substr($path, strrpos($path, '.'));
    //$extension = '.' . pathinfo($path, PATHINFO_EXTENSION);
    if ($thumbnail && !in_array($extension, ['gif', 'svg'])) {
        //if ($placeholder) $newFilename = $destPath . $thumbnail . '/' . ltrim($path, '/');
        $filesPath = utf8_substr(utf8_substr($path, 0, utf8_strrpos($path, '.')), strlen($destPath));
        $newFilename = $destPath . $thumbnail . '/' . $filesPath . $extension;
        return MEDIA_URL . ltrim($newFilename, '/');
    } else {
        return MEDIA_URL . ltrim($path, '/');
    }
}

function is_mobile() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;

    $agent = $_SERVER['HTTP_USER_AGENT'];

    return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $agent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($agent, 0, 4));
}

function break_to_array($text) {
    //return preg_split("/\r\n|\n|\r/", $text);
    return explode("\n", str_replace(["\r\n", "\r"], "\n", trim($text)));
}

function break_to_html($text, $tag = '<br>') {
    if ($tag == 'p') {
        $out = '';
        foreach (break_to_array($text) as $i) {
            if ($i) $out .= "<p>$i<p>";
        }
        return $out;
    } else {
        return str_replace(["\r\n", "\r", "\n"], $tag, $text);
    }
}

function text_overflow_ellipsis($value, $length = 150) {
    $text = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    return utf8_substr(strip_tags($text), 0, $length) . '...';
}

/**
 * @param array $data
 * @return array
 */
function query_to_object($data = array()) {
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
 * @param string $tableName
 * @param string $shortName
 * @param array $properties
 * @return string
 */
function generate_select_raw($tableName, $shortName, $properties = array()) {
    $r = [];
    foreach ($properties as $value) {
        $r[] = "`$tableName`.`$value` AS " . $shortName . "__$value";
    }

    return implode(',', $r);
}

/**
 * @param $dir
 */
function rm_child_file($dir) {
    $ffs = scandir($dir);
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..' && $ff != 'index.html') {
            if (is_file($dir . '/' . $ff)) {
                unlink($dir . '/' . $ff);
            } else if (is_dir($dir . '/' . $ff)) {
                rm_child_file($dir . '/' . $ff);
                rmdir($dir . '/' . $ff);
            }
        }
    }
}

/**
 * Fetch YouTube ID from URLs
 * @param string $url
 * @return string
 */
function youtube_id($url) {
    return preg_replace('/[^\w\-\_].*$/', '', preg_replace('/(^.*\/embed\/)|(^.*(\?|\&)v\=)/', '', $url));
}

/**
 * Fetch YouTube ID from URLs
 * @param string $url
 * @return string
 */
function fetch_youtube_id($url) {
    $regex = '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@#?&%=+\/\$_.-]*~i';
    return preg_replace($regex, '$1', $url);
}

/**
 * Get number of days between two dates
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

    $string = array(
        'y' => 'năm',  //'year',
        'm' => 'tháng',//'month',
        'w' => 'tuần', //'week',
        'd' => 'ngày', //'day',
        'h' => 'giờ',  //'hour',
        'i' => 'phút', //'minute',
        's' => 'giây', //'second',
    );
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
 * Parse data to object in object
 * @param array $data
 *
 * @return array
 */
function parse_to_respond($data = []) {
    $r = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $r[$key] = parse_to_respond($value);
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
