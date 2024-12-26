<?php
if (!function_exists('media_url_file')) {
    function media_url_file($path) {
        if (!$path) return '';
        $filesystem = config('filesystems');
        if ($filesystem['default'] == 's3') {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            return $protocol . $filesystem["disks"]['s3']['bucket'] . ".s3.amazonaws.com" . $path;
        } else {
            // remove public because of conflicting between local and s3
            //return url(str_replace("public/", "", $path));
            return $filesystem["disks"]['local']['url'] . $path;
        }
    }
}

function generate_svg_avatar($name = 'n', $bgColor = "#0365a1", $width = 200, $height = 200) {
    if (empty($name)) {
        $name = 'n';
    }
    $text = strtoupper(substr($name, 0, 1));
    $textColor = luminance($bgColor, -0.3);
    return "<svg width=\"$width\" height=\"$height\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><rect x=\"0\" y=\"0\" width=\"$height\" height=\"$height\" style=\"fill: $bgColor\"></rect><text x=\"50%\" y=\"50%\" dy=\".1em\" fill=\"$textColor\" font-weight=\"bold\" text-anchor=\"middle\" dominant-baseline=\"middle\" style=\"font-family: OpenSans, sans-serif; font-size: 160; line-height: 1\">$text</text></svg>";
}
