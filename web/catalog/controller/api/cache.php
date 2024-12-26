<?php
class ControllerApiCache extends Controller {
    private function listFiles($dir) {
        $dir = rtrim($dir, '/');
        $ffs = scandir($dir);
        $list = [];
        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..' && $ff != 'index.html' && $ff != '.gitignore') {
                if (is_file($dir . '/' . $ff)) $list[] = $dir . '/' . $ff;
                if (is_dir($dir . '/' . $ff)) $list = array_merge($list, $this->listFiles($dir . '/' . $ff));
            }
        }

        return $list;
    }

    public function index() {
        $files = $this->listFiles(DIR_CACHE);
        $count = 0;
        foreach ($files as $file) {
            unlink($file);
            $count++;
        }
        var_dump($files);
        exit();
    }
}
