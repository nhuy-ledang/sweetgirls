<?php
class ControllerStartupShortcodes extends Controller {
    public function index() {
        //=== Default shortcodes
        $this->load->helper('shortcodes_default');
        $class = new ShortcodesDefault($this->registry);
        $scDefaults = get_class_methods($class);
        foreach ($scDefaults as $shortcode) {
            $this->shortcodes->add_shortcode($shortcode, $class);
        }
        //=== Extensions shortcodes : for extensions developer
        $files = glob(DIR_APPLICATION . 'controller/shortcodes/*.php');
        if ($files) {
            foreach ($files as $file) {
                require_once($file);
                $file = basename($file, ".php");
                $extClass = 'ControllerShortcodes' . ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $file));
                $class = new $extClass($this->registry);
                $scExtensions = get_class_methods($class);
                foreach ($scExtensions as $shortcode) {
                    $this->shortcodes->add_shortcode($shortcode, $class);
                }
            }
        }
    }
}
