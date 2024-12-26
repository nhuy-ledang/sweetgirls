<?php
/**
 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
 * @param string $hex Colour as hexadecimal (with or without hash);
 * @percent float $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
 * @return string Lightened/Darkend colour as hexadecimal (with hash);
 */
function luminance($hex, $percent) {
    if (strlen($hex) < 6) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $hex = array_map('hexdec', str_split(str_pad(str_replace('#', '', $hex), 6, '0'), 2));

    foreach ($hex as $i => $color) {
        $from = $percent < 0 ? 0 : $color;
        $to = $percent < 0 ? $color : 255;
        $pvalue = ceil(($to - $from) * $percent);
        $hex[$i] = str_pad(dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hex);
}

/**
 * Convert hexdec color string to rgb(a) string
 *
 * @param $color
 * @param bool $opacity
 * @return string
 */
function hex2rgba($color, $opacity = false) {
    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if (empty($color))
        return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
    } else if (strlen($color) == 3) {
        $hex = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if ($opacity) {
        if (abs($opacity) > 1) $opacity = 1.0;
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }

    //Return rgb(a) color string
    return $output;
}
//
///****************************************************************
// * https://gist.github.com/olimortimer/4223236
// ***************************************************************/
//
///**
// * Makes sure the input we receive will be returned as a valid rgb array
// *
// * @param array|int|false $r
// * @param int|false $g
// * @param int|false $b
// * @return int
// */
//function _rgb_valid($r, $g = false, $b = false) {
//    if (is_array($r)) {
//        $rgb = $r;
//    } else {
//        $rgb = [];
//        $rgb['r'] = $r;
//        $rgb['g'] = $b;
//        $rgb['b'] = $b;
//    }
//
//    if (empty($rgb['r']) || (ctype_digit($rgb['r']) && $rgb['r'] > 0 && $rgb['r'] < 256 ? $rgb['r'] : 0)) {
//        $rgb['r'] = 0;
//    }
//
//    if (empty($rgb['g']) || (ctype_digit($rgb['g']) && $rgb['g'] > 0 && $rgb['g'] < 256 ? $rgb['g'] : 0)) {
//        $rgb['g'] = 0;
//    }
//
//    if (empty($rgb['b']) || (ctype_digit($rgb['b']) && $rgb['b'] > 0 && $rgb['b'] < 256 ? $rgb['b'] : 0)) {
//        $rgb['b'] = 0;
//    }
//
//    return $rgb;
//}
//
///**
// * Pretty accurate way of calculating the Luma (Brightness) value
// *
// * @param array|int|false $r
// * @param int|false $g
// * @param int|false $b
// * @return int
// */
//function luma($r, $g = false, $b = false) {
//    $rgb = _rgb_valid($r, $g, $b);
//
//    return (0.2126 * $rgb['r']) + (0.7152 * $rgb['g']) + (0.0722 * $rgb['b']);
//}
//
///**
// * Fast but less accurate way of calculating the Luma (Brightness) value
// *
// * @param array|int|false $r
// * @param int|false $g
// * @param int|false $b
// * @return int
// */
//function luma_fast($r, $g = false, $b = false) {
//    $rgb = _rgb_valid($r, $g, $b);
//
//    return ($rgb['r'] * 2) + $rgb['b'] + ($rgb['g'] * 3) / 6;
//}
//
//function hex2css_rgb($hex) {
//    $rgb = hex2rgb($hex);
//
//    return sprintf('rgb(%d,%d,%d)', $rgb['r'], $rgb['g'], $rgb['b']);
//
//}
//
//function rgb_change_brightness_percent($percent, $r, $g = false, $b = false) {
//    $rgb = _rgb_valid($r, $g, $b);
//
//    $percent = abs($percent);
//
//    foreach (['r', 'g', 'b'] as $i) {
//        $rgb[$i] = (int)($rgb[$i] * $percent);
//
//        if ($rgb[$i] > 255) $rgb[$i] = 255;
//    }
//
//    return $rgb;
//}
//
///**
// * Returns rgb array from a hex color value
// *
// * @param string $hex
// * @return array
// */
//function hex2rgb($hex) {
//    $hex = hex_color_expand($hex);
//
//    $rgb = [];
//
//    $rgb['r'] = hexdec(substr($hex, 0, 2));
//    $rgb['g'] = hexdec(substr($hex, 2, 2));
//    $rgb['b'] = hexdec(substr($hex, 4, 2));
//
//    return $rgb;
//}
//
///**
// * @param $r
// * @param bool $g
// * @param bool $b
// * @return string
// */
//function rgb2hex($r, $g = false, $b = false) {
//    $rgb = _rgb_valid($r, $g, $b);
//
//    return dechex($rgb['r']) . dechex($rgb['g']) . dechex($rgb['b']);
//}
//
///**
// * Expands a shorthand hex color to a full hex color
// *
// * @param string $hex
// * @return string
// */
//function hex_color_expand($hex = false) {
//    if (strlen($hex) < 1) return '000000';
//
//    if ($hex[0] == '#') $hex = substr($hex, 1);
//
//    $hex_len = strlen($hex);
//
//    //ascii range in utf8 is preserved and as we're working with hex, we should only have values from the ascii range so strlen should be safe
//    //still check for 0 as we remove the #
//    if ($hex_len < 1) return '000000';
//
//    if ($hex_len > 3) {
//        //we can't auto expand this so just ass f's on the end or if it's too long, trim it'
//        if ($hex_len > 6) {
//            $hex = substr($hex, 0, 6);
//        } else {
//            $hex = str_pad($hex, 6, '0', STR_PAD_RIGHT);
//        }
//    } else {
//        //we have a value that we can duplicate ie f, ff, fff   to become #FFFFFF
//        $repeat = (6 / $hex_len) - 1; //-1 as we already have 1 instance
//        $segment = $hex;
//        $hex = str_pad($hex, 6, $segment, STR_PAD_RIGHT);
//    }
//
//    return $hex;
//}
//
///**
// * Convert rgb to Hue, Saturation and Brightness
// *
// * @param array|int|false $r
// * @param int|false $g
// * @param int|false $b
// * @return array
// */
//function rgb2hsv($r, $g = false, $b = false) {
//    $rgb = _rgb_valid($r, $g, $b);
//
//    $HSL = [];
//
//    $var_R = ($rgb['r'] / 255);
//    $var_G = ($rgb['g'] / 255);
//    $var_B = ($rgb['b'] / 255);
//
//    $var_Min = min($var_R, $var_G, $var_B);
//    $var_Max = max($var_R, $var_G, $var_B);
//    $del_Max = $var_Max - $var_Min;
//
//    $V = $var_Max;
//
//    if ($del_Max == 0) {
//        $H = 0;
//        $S = 0;
//    } else {
//        $S = $del_Max / $var_Max;
//
//        $del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
//        $del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
//        $del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;
//
//        if ($var_R == $var_Max) $H = $del_B - $del_G;
//        else if ($var_G == $var_Max) $H = (1 / 3) + $del_R - $del_B;
//        else if ($var_B == $var_Max) $H = (2 / 3) + $del_G - $del_R;
//
//        if ($H < 0) $H++;
//        if ($H > 1) $H--;
//    }
//
//    $HSL['H'] = $H;
//    $HSL['S'] = $S;
//    $HSL['V'] = $V;
//
//    return $HSL;
//}
//
//function HSVtoRGB(array $hsv) {
//    $H = $hsv['H'] / 255;
//    $S = $hsv['S'] / 255;
//    $V = $hsv['V'] / 255;
//    //1
//    $H *= 6;
//    //2
//    $I = floor($H);
//    $F = $H - $I;
//    //3
//    $M = $V * (1 - $S);
//    $N = $V * (1 - $S * $F);
//    $K = $V * (1 - $S * (1 - $F));
//    //4
//    switch ($I) {
//        case 0:
//            list($R, $G, $B) = [$V, $K, $M];
//            break;
//        case 1:
//            list($R, $G, $B) = [$N, $V, $M];
//            break;
//        case 2:
//            list($R, $G, $B) = [$M, $V, $K];
//            break;
//        case 3:
//            list($R, $G, $B) = [$M, $N, $V];
//            break;
//        case 4:
//            list($R, $G, $B) = [$K, $M, $V];
//            break;
//        case 5:
//        case 6: //for when $H=1 is given
//            list($R, $G, $B) = [$V, $M, $N];
//            break;
//    }
//    return ['r' => $R * 255, 'g' => $G * 255, 'b' => $B * 255];
//}
