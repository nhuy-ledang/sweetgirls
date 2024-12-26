<?php namespace Modules\Core\Common\Utilhelper;

class Utilhelper {
    public static function load_route($module, $group = 'api') {
        $path = app_path('Http/Routes/' . $module . '/' . $group . 'Routes.php');
        if (file_exists($path)) {
            return $path;
        } else {
            $path = base_path('Modules/' . $module . '/Http/Routes/' . $group . 'Routes.php');
            if (file_exists($path)) {
                return $path;
            } else {
                return false;
            }
        }
    }
}