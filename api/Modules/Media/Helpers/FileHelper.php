<?php namespace Modules\Media\Helpers;

use Illuminate\Support\Str;

class FileHelper {
    public static function slug($name) {
        $extension = self::getExtension($name);
        $name = str_replace($extension, '', $name);

        $name = Str::slug($name);

        return $name . $extension;
    }

    public static function limit($name, $length) {
        $extension = self::getExtension($name);
        $name = str_replace($extension, '', $name);
        $name = Str::limit($name, $length, '');
        return $name . $extension;
    }

    /**
     * Get the extension from the given name
     * @param $name
     * @return string
     */
    public static function getExtension($name) {
        return substr($name, strrpos($name, '.'));
    }
}
