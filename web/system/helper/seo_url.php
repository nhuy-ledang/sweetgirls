<?php
function utf8_to_ascii($str) {
    if (!$str) return false;
    $unicode = array(
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
    );
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    return $str;
}

/*function utf8_to_ascii2($str) {
    $chars = array(
        'a' => array('ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'á', 'à', 'ả', 'ã', 'ạ', 'â', 'ă', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Â', 'Ă'),
        'e' => array('ế', 'ề', 'ể', 'ễ', 'ệ', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê'),
        'i' => array('í', 'ì', 'ỉ', 'ĩ', 'ị', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị'),
        'o' => array('ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'Ố', 'Ồ', 'Ổ', 'Ô', 'Ộ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ơ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ơ'),
        'u' => array('ứ', 'ừ', 'ử', 'ữ', 'ự', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư'),
        'y' => array('ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'),
        'd' => array('đ', 'Đ'),
    );
    foreach ($chars as $key => $arr) {
        foreach ($arr as $val) {
            $str = str_replace($val, $key, $str);
        }
    }

    return strtolower($str);
}*/

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
    $str = trim($str);
    $old = substr($str, -5, 5) === '.html';
    if ($old) $str = substr($str, 0, strlen($str) - 5);
    $str = str_replace('&amp;', ' ', $str);
    $str = to_alias($str);

    return $old ? ($str . '.html') : $str;
}
