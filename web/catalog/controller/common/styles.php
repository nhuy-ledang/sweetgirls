<?php
class ControllerCommonStyles extends Controller {
    private function getColorStyles() {
//        $blue = $this->config->get('config_color_blue');
//        $green = $this->config->get('config_color_green');
//        $cyan = $this->config->get('config_color_cyan');
//        $yellow = $this->config->get('config_color_yellow');
//        $red = $this->config->get('config_color_red');
//        $gray = $this->config->get('config_color_gray');
//        $gray_dark = $this->config->get('config_color_gray_dark');
//        $indigo = $this->config->get('config_color_indigo');
//        $purple = $this->config->get('config_color_purple');
//        $pink = $this->config->get('config_color_pink');
//        $orange = $this->config->get('config_color_orange');
//        $teal = $this->config->get('config_color_teal');
//        $colors = ['blue', 'green', 'cyan', 'yellow', 'red', 'gray', 'gray_dark', 'indigo', 'purple', 'pink', 'orange', 'teal'];
//        $pr = $this->config->get('config_color_primary');
//        $primary = in_array($pr, $colors) ? $$pr : false;
//        $se = $this->config->get('config_color_secondary');
//        $secondary = in_array($pr, $colors) ? $$se : false;
//        $su = $this->config->get('config_color_success');
//        $success = in_array($pr, $colors) ? $$su : false;
        $primary = $this->config->get('config_color_primary');
        $secondary = $this->config->get('config_color_secondary');
        $success = $this->config->get('config_color_success');
        $styles = '';
        foreach (['', 'o-'] as $pre) {
//            if ($blue) $styles .= ".{$pre}bg-blue{background-color:$blue !important;}a.bg-blue:hover,a.bg-blue:focus,button.bg-blue:hover,button.bg-blue:focus{background-color:#1c316c !important;}.text-blue{color:$blue !important;}a.text-blue:hover,a.text-blue:focus{color:#162858 !important;}";
//            if ($green) $styles .= ".{$pre}bg-green{background-color:$green !important;}a.bg-green:hover,a.bg-green:focus,button.bg-green:hover,button.bg-green:focus{background-color:#1e7e34 !important;}.text-green{color:$green !important;}a.text-green:hover,a.text-green:focus{color:#19692c !important;}";
//            if ($cyan) $styles .= ".{$pre}bg-cyan{background-color:$cyan !important;}a.bg-cyan:hover,a.bg-cyan:focus,button.bg-cyan:hover,button.bg-cyan:focus{background-color:#117a8b !important;}.text-cyan{color:$cyan !important;}a.text-cyan:hover,a.text-cyan:focus{color:#0f6674 !important;}";
//            if ($yellow) $styles .= ".{$pre}bg-yellow{background-color:$yellow !important;}a.bg-yellow:hover,a.bg-yellow:focus,button.bg-yellow:hover,button.bg-yellow:focus{background-color:#d39e00 !important;}.text-yellow{color:$yellow !important;}a.text-yellow:hover,a.text-yellow:focus{color:#ba8b00 !important;}";
//            if ($red) $styles .= ".{$pre}bg-red{background-color:$red !important;}a.bg-red:hover,a.bg-red:focus,button.bg-red:hover,button.bg-red:focus{background-color:#bd2130 !important;}.text-red{color:$red !important;}a.text-red:hover,a.text-red:focus{color:#a71d2a !important;}";
//            if ($gray) $styles .= ".{$pre}bg-gray{background-color:$gray !important;}a.bg-gray:hover,a.bg-gray:focus,button.bg-gray:hover,button.bg-gray:focus{background-color:#7f7f7f !important;}.text-gray{color:$gray !important;}a.text-gray:hover,a.text-gray:focus{color:#727272 !important;}";
//            if ($gray_dark) $styles .= ".{$pre}bg-gray-dark{background-color:$gray_dark !important;}a.bg-gray-dark:hover,a.bg-gray-dark:focus,button.bg-gray-dark:hover,button.bg-gray-dark:focus{background-color:#303030 !important;}.text-gray-dark{color:$gray_dark !important;}a.text-gray-dark:hover,a.text-gray-dark:focus{color:#232323 !important;}";
//            if ($indigo) $styles .= ".{$pre}bg-indigo{background-color:$indigo !important;}a.bg-indigo:hover,a.bg-indigo:focus,button.bg-indigo:hover,button.bg-indigo:focus{background-color:#510bc4 !important;}.text-indigo{color:$indigo !important;}a.text-indigo:hover,a.text-indigo:focus{color:#4709ac !important;}";
//            if ($purple) $styles .= ".{$pre}bg-purple{background-color:$purple !important;}a.bg-purple:hover,a.bg-purple:focus,button.bg-purple:hover,button.bg-purple:focus{background-color:#59339d !important;}.text-purple{color:$purple !important;}a.text-purple:hover,a.text-purple:focus{color:#4e2d89 !important;}";
//            if ($pink) $styles .= ".{$pre}bg-pink{background-color:$pink !important;}a.bg-pink:hover,a.bg-pink:focus,button.bg-pink:hover,button.bg-pink:focus{background-color:#d91a72 !important;}.text-pink{color:$pink !important;}a.text-pink:hover,a.text-pink:focus{color:#c21766 !important;}";
//            if ($orange) $styles .= ".{$pre}bg-orange{background-color:$orange !important;}a.bg-orange:hover,a.bg-orange:focus,button.bg-orange:hover,button.bg-orange:focus{background-color:#dc6502 !important;}.text-orange{color:$orange !important;}a.text-orange:hover,a.text-orange:focus{color:#c35a02 !important;}";
//            if ($teal) $styles .= ".{$pre}bg-teal{background-color:$teal !important;}a.bg-teal:hover,a.bg-teal:focus,button.bg-teal:hover,button.bg-teal:focus{background-color:#199d76 !important;}.text-teal{color:$teal !important;}a.text-teal:hover,a.text-teal:focus{color:#158765 !important;}";
            if ($primary) $styles .= ".{$pre}bg-primary{background-color:$primary !important;}a.bg-primary:hover,a.bg-primary:focus,button.bg-primary:hover,button.bg-primary:focus{background-color:{$primary}dd !important;}.text-primary{color:$primary !important;}a.text-primary:hover,a.text-primary:focus{color:{$primary}dd !important;}";
            if ($secondary) $styles .= ".{$pre}bg-secondary{background-color:$secondary !important;}a.bg-secondary:hover,a.bg-secondary:focus,button.bg-secondary:hover,button.bg-secondary:focus{background-color:{$secondary}dd !important;}.text-secondary{color:$secondary !important;}a.text-secondary:hover,a.text-secondary:focus{color:{$secondary}dd !important;}";
            if ($success) $styles .= ".{$pre}bg-success{background-color:$success !important;}a.bg-success:hover,a.bg-success:focus,button.bg-success:hover,button.bg-success:focus{background-color:{$success}dd !important;}.text-success{color:$success !important;}a.text-success:hover,a.text-success:focus{color:{$success}dd !important;}";
            $styles .= ".{$pre}bg-black{background-color:#000000 !important;}a.bg-black:hover,a.bg-black:focus,button.bg-black:hover,button.bg-black:focus{background-color:#1e7e34 !important;}.text-black{color:#000000 !important;}a.text-black:hover,a.text-black:focus{color:#19692c !important;}";
        }
        $root = '';
        if ($primary) $root .= "--primary: $primary;";
        if ($secondary) $root .= "--secondary: $secondary;";
        if ($success) $root .= "--success: $success;";
        if ($root) $styles .= ":root{{$root}}";

        return $styles;
    }

    private function getSpacing() {
        $styles = '';
        $s1 = $this->config->get('config_spacing1');
        $s2 = $this->config->get('config_spacing2');
        $s3 = $this->config->get('config_spacing3');
        $s4 = $this->config->get('config_spacing4');
        $s5 = $this->config->get('config_spacing5');
        $s6 = $this->config->get('config_spacing6');
        $s7 = $this->config->get('config_spacing7');
        $s8 = $this->config->get('config_spacing8');
        $s9 = $this->config->get('config_spacing9');
        $s10 = $this->config->get('config_spacing10');
        $s11 = $this->config->get('config_spacing11');
        $s12 = $this->config->get('config_spacing12');
        $s13 = $this->config->get('config_spacing13');
        $s14 = $this->config->get('config_spacing14');
        $s15 = $this->config->get('config_spacing15');

        foreach (['', 'o-'] as $pre) {
            if ($s1) {
                $styles .= ".m-1{margin:$s1 !important}.{$pre}mt-1,.my-1{margin-top:$s1 !important}.mr-1,.mx-1{margin-right:$s1 !important}.{$pre}mb-1,.my-1{margin-bottom:$s1 !important}.ml-1,.mx-1{margin-left:$s1 !important}";
                $styles .= ".p-1{padding:$s1 !important}.{$pre}pt-1,.py-1{padding-top:$s1 !important}.pr-1,.px-1{padding-right:$s1 !important}.{$pre}pb-1,.py-1{padding-bottom:$s1 !important}.pl-1,.px-1{padding-left:$s1 !important}";
            }

            if ($s2) {
                $styles .= ".m-2{margin:$s2 !important}.{$pre}mt-2,.my-2{margin-top:$s2 !important}.mr-2,.mx-2{margin-right:$s2 !important}.{$pre}mb-2,.my-2{margin-bottom:$s2 !important}.ml-2,.mx-2{margin-left:$s2 !important}";
                $styles .= ".p-2{padding:$s2 !important}.{$pre}pt-2,.py-2{padding-top:$s2 !important}.pr-2,.px-2{padding-right:$s2 !important}.{$pre}pb-2,.py-2{padding-bottom:$s2 !important}.pl-2,.px-2{padding-left:$s2 !important}";
            }

            if ($s3) {
                $styles .= ".m-3{margin:$s3 !important}.{$pre}mt-3,.my-3{margin-top:$s3 !important}.mr-3,.mx-3{margin-right:$s3 !important}.{$pre}mb-3,.my-3{margin-bottom:$s3 !important}.ml-3,.mx-3{margin-left:$s3 !important}";
                $styles .= ".p-3{padding:$s3 !important}.{$pre}pt-3,.py-3{padding-top:$s3 !important}.pr-3,.px-3{padding-right:$s3 !important}.{$pre}pb-3,.py-3{padding-bottom:$s3 !important}.pl-3,.px-3{padding-left:$s3 !important}";
            }

            if ($s4) {
                $styles .= ".m-4{margin:$s4 !important}.{$pre}mt-4,.my-4{margin-top:$s4 !important}.mr-4,.mx-4{margin-right:$s4 !important}.{$pre}mb-4,.my-4{margin-bottom:$s4 !important}.ml-4,.mx-4{margin-left:$s4 !important}";
                $styles .= ".p-4{padding:$s4 !important}.{$pre}pt-4,.py-4{padding-top:$s4 !important}.pr-4,.px-4{padding-right:$s4 !important}.{$pre}pb-4,.py-4{padding-bottom:$s4 !important}.pl-4,.px-4{padding-left:$s4 !important}";
            }

            if ($s5) {
                $styles .= ".m-5{margin:$s5 !important}.{$pre}mt-5,.my-5{margin-top:$s5 !important}.mr-5,.mx-5{margin-right:$s5 !important}.{$pre}mb-5,.my-5{margin-bottom:$s5 !important}.ml-5,.mx-5{margin-left:$s5 !important}";
                $styles .= ".p-5{padding:$s5 !important}.{$pre}pt-5,.py-5{padding-top:$s5 !important}.pr-5,.px-5{padding-right:$s5 !important}.{$pre}pb-5,.py-5{padding-bottom:$s5 !important}.pl-5,.px-5{padding-left:$s5 !important}";
            }

            if ($s6) {
                $styles .= ".m-6{margin:$s6 !important}.{$pre}mt-6,.my-6{margin-top:$s6 !important}.mr-6,.mx-6{margin-right:$s6 !important}.{$pre}mb-6,.my-6{margin-bottom:$s6 !important}.ml-6,.mx-6{margin-left:$s6 !important}";
                $styles .= ".p-6{padding:$s6 !important}.{$pre}pt-6,.py-6{padding-top:$s6 !important}.pr-6,.px-6{padding-right:$s6 !important}.{$pre}pb-6,.py-6{padding-bottom:$s6 !important}.pl-6,.px-6{padding-left:$s6 !important}";
            }

            if ($s7) {
                $styles .= ".m-7{margin:$s7 !important}.{$pre}mt-7,.my-7{margin-top:$s7 !important}.mr-7,.mx-7{margin-right:$s7 !important}.{$pre}mb-7,.my-7{margin-bottom:$s7 !important}.ml-7,.mx-7{margin-left:$s7 !important}";
                $styles .= ".p-7{padding:$s7 !important}.{$pre}pt-7,.py-7{padding-top:$s7 !important}.pr-7,.px-7{padding-right:$s7 !important}.{$pre}pb-7,.py-7{padding-bottom:$s7 !important}.pl-7,.px-7{padding-left:$s7 !important}";
            }

            if ($s8) {
                $styles .= ".m-8{margin:$s8 !important}.{$pre}mt-8,.my-8{margin-top:$s8 !important}.mr-8,.mx-8{margin-right:$s8 !important}.{$pre}mb-8,.my-8{margin-bottom:$s8 !important}.ml-8,.mx-8{margin-left:$s8 !important}";
                $styles .= ".p-8{padding:$s8 !important}.{$pre}pt-8,.py-8{padding-top:$s8 !important}.pr-8,.px-8{padding-right:$s8 !important}.{$pre}pb-8,.py-8{padding-bottom:$s8 !important}.pl-8,.px-8{padding-left:$s8 !important}";
            }

            if ($s9) {
                $styles .= ".m-9{margin:$s9 !important}.{$pre}mt-9,.my-9{margin-top:$s9 !important}.mr-9,.mx-9{margin-right:$s9 !important}.{$pre}mb-9,.my-9{margin-bottom:$s9 !important}.ml-9,.mx-9{margin-left:$s9 !important}";
                $styles .= ".p-9{padding:$s9 !important}.{$pre}pt-9,.py-9{padding-top:$s9 !important}.pr-9,.px-9{padding-right:$s9 !important}.{$pre}pb-9,.py-9{padding-bottom:$s9 !important}.pl-9,.px-9{padding-left:$s9 !important}";
            }

            if ($s10) {
                $styles .= ".m-10{margin:$s10 !important}.{$pre}mt-10,.my-10{margin-top:$s10 !important}.mr-10,.mx-10{margin-right:$s10 !important}.{$pre}mb-10,.my-10{margin-bottom:$s10 !important}.ml-10,.mx-10{margin-left:$s10 !important}";
                $styles .= ".p-10{padding:$s10 !important}.{$pre}pt-10,.py-10{padding-top:$s10 !important}.pr-10,.px-10{padding-right:$s10 !important}.{$pre}pb-10,.py-10{padding-bottom:$s10 !important}.pl-10,.px-10{padding-left:$s10 !important}";
            }

            if ($s11) {
                $styles .= ".m-11{margin:$s11 !important}.{$pre}mt-11,.my-11{margin-top:$s11 !important}.mr-11,.mx-11{margin-right:$s11 !important}.{$pre}mb-11,.my-11{margin-bottom:$s11 !important}.ml-11,.mx-11{margin-left:$s11 !important}";
                $styles .= ".p-11{padding:$s11 !important}.{$pre}pt-11,.py-11{padding-top:$s11 !important}.pr-11,.px-11{padding-right:$s11 !important}.{$pre}pb-11,.py-11{padding-bottom:$s11 !important}.pl-11,.px-11{padding-left:$s11 !important}";
            }

            if ($s12) {
                $styles .= ".m-12{margin:$s12 !important}.{$pre}mt-12,.my-12{margin-top:$s12 !important}.mr-12,.mx-12{margin-right:$s12 !important}.{$pre}mb-12,.my-12{margin-bottom:$s12 !important}.ml-12,.mx-12{margin-left:$s12 !important}";
                $styles .= ".p-12{padding:$s12 !important}.{$pre}pt-12,.py-12{padding-top:$s12 !important}.pr-12,.px-12{padding-right:$s12 !important}.{$pre}pb-12,.py-12{padding-bottom:$s12 !important}.pl-12,.px-12{padding-left:$s12 !important}";
            }

            if ($s13) {
                $styles .= ".m-13{margin:$s13 !important}.{$pre}mt-13,.my-13{margin-top:$s13 !important}.mr-13,.mx-13{margin-right:$s13 !important}.{$pre}mb-13,.my-13{margin-bottom:$s13 !important}.ml-13,.mx-13{margin-left:$s13 !important}";
                $styles .= ".p-13{padding:$s13 !important}.{$pre}pt-13,.py-13{padding-top:$s13 !important}.pr-13,.px-13{padding-right:$s13 !important}.{$pre}pb-13,.py-13{padding-bottom:$s13 !important}.pl-13,.px-13{padding-left:$s13 !important}";
            }

            if ($s14) {
                $styles .= ".m-14{margin:$s14 !important}.{$pre}mt-14,.my-14{margin-top:$s14 !important}.mr-14,.mx-14{margin-right:$s14 !important}.{$pre}mb-14,.my-14{margin-bottom:$s14 !important}.ml-14,.mx-14{margin-left:$s14 !important}";
                $styles .= ".p-14{padding:$s14 !important}.{$pre}pt-14,.py-14{padding-top:$s14 !important}.pr-14,.px-14{padding-right:$s14 !important}.{$pre}pb-14,.py-14{padding-bottom:$s14 !important}.pl-14,.px-14{padding-left:$s14 !important}";
            }

            if ($s15) {
                $styles .= ".m-15{margin:$s15 !important}.{$pre}mt-15,.my-15{margin-top:$s15 !important}.mr-15,.mx-15{margin-right:$s15 !important}.{$pre}mb-15,.my-15{margin-bottom:$s15 !important}.ml-15,.mx-15{margin-left:$s15 !important}";
                $styles .= ".p-15{padding:$s15 !important}.{$pre}pt-15,.py-15{padding-top:$s15 !important}.pr-15,.px-15{padding-right:$s15 !important}.{$pre}pb-15,.py-15{padding-bottom:$s15 !important}.pl-15,.px-15{padding-left:$s15 !important}";
            }

            $xl = '';
            if ($s1) {
                $xl .= ".m-xl-1{margin:$s1 !important}.{$pre}mt-xl-1,.my-xl-1{margin-top:$s1 !important}.mr-xl-1,.mx-xl-1{margin-right:$s1 !important}.{$pre}mb-xl-1,.my-xl-1{margin-bottom:$s1 !important}.ml-xl-1,.mx-xl-1{margin-left:$s1 !important}";
                $xl .= ".p-xl-1{padding:$s1 !important}.{$pre}pt-xl-1,.py-xl-1{padding-top:$s1 !important}.pr-xl-1,.px-xl-1{padding-right:$s1 !important}.{$pre}pb-xl-1,.py-xl-1{padding-bottom:$s1 !important}.pl-xl-1,.px-xl-1{padding-left:$s1 !important}";
            }
            if ($s2) {
                $xl .= ".m-xl-2{margin:$s2 !important}.{$pre}mt-xl-2,.my-xl-2{margin-top:$s2 !important}.mr-xl-2,.mx-xl-2{margin-right:$s2 !important}.{$pre}mb-xl-2,.my-xl-2{margin-bottom:$s2 !important}.ml-xl-2,.mx-xl-2{margin-left:$s2 !important}";
                $xl .= ".p-xl-2{padding:$s2 !important}.{$pre}pt-xl-2,.py-xl-2{padding-top:$s2 !important}.pr-xl-2,.px-xl-2{padding-right:$s2 !important}.{$pre}pb-xl-2,.py-xl-2{padding-bottom:$s2 !important}.pl-xl-2,.px-xl-2{padding-left:$s2 !important}";
            }
            if ($s3) {
                $xl .= ".m-xl-3{margin:$s3 !important}.{$pre}mt-xl-3,.my-xl-3{margin-top:$s3 !important}.mr-xl-3,.mx-xl-3{margin-right:$s3 !important}.{$pre}mb-xl-3,.my-xl-3{margin-bottom:$s3 !important}.ml-xl-3,.mx-xl-3{margin-left:$s3 !important}";
                $xl .= ".p-xl-3{padding:$s3 !important}.{$pre}pt-xl-3,.py-xl-3{padding-top:$s3 !important}.pr-xl-3,.px-xl-3{padding-right:$s3 !important}.{$pre}pb-xl-3,.py-xl-3{padding-bottom:$s3 !important}.pl-xl-3,.px-xl-3{padding-left:$s3 !important}";
            }
            if ($s4) {
                $xl .= ".m-xl-4{margin:$s4 !important}.{$pre}mt-xl-4,.my-xl-4{margin-top:$s4 !important}.mr-xl-4,.mx-xl-4{margin-right:$s4 !important}.{$pre}mb-xl-4,.my-xl-4{margin-bottom:$s4 !important}.ml-xl-4,.mx-xl-4{margin-left:$s4 !important}";
                $xl .= ".p-xl-4{padding:$s4 !important}.{$pre}pt-xl-4,.py-xl-4{padding-top:$s4 !important}.pr-xl-4,.px-xl-4{padding-right:$s4 !important}.{$pre}pb-xl-4,.py-xl-4{padding-bottom:$s4 !important}.pl-xl-4,.px-xl-4{padding-left:$s4 !important}";
            }
            if ($s5) {
                $xl .= ".m-xl-5{margin:$s5 !important}.{$pre}mt-xl-5,.my-xl-5{margin-top:$s5 !important}.mr-xl-5,.mx-xl-5{margin-right:$s5 !important}.{$pre}mb-xl-5,.my-xl-5{margin-bottom:$s5 !important}.ml-xl-5,.mx-xl-5{margin-left:$s5 !important}";
                $xl .= ".p-xl-5{padding:$s5 !important}.{$pre}pt-xl-5,.py-xl-5{padding-top:$s5 !important}.pr-xl-5,.px-xl-5{padding-right:$s5 !important}.{$pre}pb-xl-5,.py-xl-5{padding-bottom:$s5 !important}.pl-xl-5,.px-xl-5{padding-left:$s5 !important}";
            }
            if ($s6) {
                $xl .= ".m-xl-6{margin:$s6 !important}.{$pre}mt-xl-6,.my-xl-6{margin-top:$s6 !important}.mr-xl-6,.mx-xl-6{margin-right:$s6 !important}.{$pre}mb-xl-6,.my-xl-6{margin-bottom:$s6 !important}.ml-xl-6,.mx-xl-6{margin-left:$s6 !important}";
                $xl .= ".p-xl-6{padding:$s6 !important}.{$pre}pt-xl-6,.py-xl-6{padding-top:$s6 !important}.pr-xl-6,.px-xl-6{padding-right:$s6 !important}.{$pre}pb-xl-6,.py-xl-6{padding-bottom:$s6 !important}.pl-xl-6,.px-xl-6{padding-left:$s6 !important}";
            }
            if ($s7) {
                $xl .= ".m-xl-7{margin:$s7 !important}.{$pre}mt-xl-7,.my-xl-7{margin-top:$s7 !important}.mr-xl-7,.mx-xl-7{margin-right:$s7 !important}.{$pre}mb-xl-7,.my-xl-7{margin-bottom:$s7 !important}.ml-xl-7,.mx-xl-7{margin-left:$s7 !important}";
                $xl .= ".p-xl-7{padding:$s7 !important}.{$pre}pt-xl-7,.py-xl-7{padding-top:$s7 !important}.pr-xl-7,.px-xl-7{padding-right:$s7 !important}.{$pre}pb-xl-7,.py-xl-7{padding-bottom:$s7 !important}.pl-xl-7,.px-xl-7{padding-left:$s7 !important}";
            }
            if ($s8) {
                $xl .= ".m-xl-8{margin:$s8 !important}.{$pre}mt-xl-8,.my-xl-8{margin-top:$s8 !important}.mr-xl-8,.mx-xl-8{margin-right:$s8 !important}.{$pre}mb-xl-8,.my-xl-8{margin-bottom:$s8 !important}.ml-xl-8,.mx-xl-8{margin-left:$s8 !important}";
                $xl .= ".p-xl-8{padding:$s8 !important}.{$pre}pt-xl-8,.py-xl-8{padding-top:$s8 !important}.pr-xl-8,.px-xl-8{padding-right:$s8 !important}.{$pre}pb-xl-8,.py-xl-8{padding-bottom:$s8 !important}.pl-xl-8,.px-xl-8{padding-left:$s8 !important}";
            }
            if ($s9) {
                $xl .= ".m-xl-9{margin:$s9 !important}.{$pre}mt-xl-9,.my-xl-9{margin-top:$s9 !important}.mr-xl-9,.mx-xl-9{margin-right:$s9 !important}.{$pre}mb-xl-9,.my-xl-9{margin-bottom:$s9 !important}.ml-xl-9,.mx-xl-9{margin-left:$s9 !important}";
                $xl .= ".p-xl-9{padding:$s9 !important}.{$pre}pt-xl-9,.py-xl-9{padding-top:$s9 !important}.pr-xl-9,.px-xl-9{padding-right:$s9 !important}.{$pre}pb-xl-9,.py-xl-9{padding-bottom:$s9 !important}.pl-xl-9,.px-xl-9{padding-left:$s9 !important}";
            }
            if ($s10) {
                $xl .= ".m-xl-10{margin:$s10 !important}.{$pre}mt-xl-10,.my-xl-10{margin-top:$s10 !important}.mr-xl-10,.mx-xl-10{margin-right:$s10 !important}.{$pre}mb-xl-10,.my-xl-10{margin-bottom:$s10 !important}.ml-xl-10,.mx-xl-10{margin-left:$s10 !important}";
                $xl .= ".p-xl-10{padding:$s10 !important}.{$pre}pt-xl-10,.py-xl-10{padding-top:$s10 !important}.pr-xl-10,.px-xl-10{padding-right:$s10 !important}.{$pre}pb-xl-10,.py-xl-10{padding-bottom:$s10 !important}.pl-xl-10,.px-xl-10{padding-left:$s10 !important}";
            }
            if ($s11) {
                $xl .= ".m-xl-11{margin:$s11 !important}.{$pre}mt-xl-11,.my-xl-11{margin-top:$s11 !important}.mr-xl-11,.mx-xl-11{margin-right:$s11 !important}.{$pre}mb-xl-11,.my-xl-11{margin-bottom:$s11 !important}.ml-xl-11,.mx-xl-11{margin-left:$s11 !important}";
                $xl .= ".p-xl-11{padding:$s11 !important}.{$pre}pt-xl-11,.py-xl-11{padding-top:$s11 !important}.pr-xl-11,.px-xl-11{padding-right:$s11 !important}.{$pre}pb-xl-11,.py-xl-11{padding-bottom:$s11 !important}.pl-xl-11,.px-xl-11{padding-left:$s11 !important}";
            }
            if ($s12) {
                $xl .= ".m-xl-12{margin:$s12 !important}.{$pre}mt-xl-12,.my-xl-12{margin-top:$s12 !important}.mr-xl-12,.mx-xl-12{margin-right:$s12 !important}.{$pre}mb-xl-12,.my-xl-12{margin-bottom:$s12 !important}.ml-xl-12,.mx-xl-12{margin-left:$s12 !important}";
                $xl .= ".p-xl-12{padding:$s12 !important}.{$pre}pt-xl-12,.py-xl-12{padding-top:$s12 !important}.pr-xl-12,.px-xl-12{padding-right:$s12 !important}.{$pre}pb-xl-12,.py-xl-12{padding-bottom:$s12 !important}.pl-xl-12,.px-xl-12{padding-left:$s12 !important}";
            }
            if ($s13) {
                $xl .= ".m-xl-13{margin:$s13 !important}.{$pre}mt-xl-13,.my-xl-13{margin-top:$s13 !important}.mr-xl-13,.mx-xl-13{margin-right:$s13 !important}.{$pre}mb-xl-13,.my-xl-13{margin-bottom:$s13 !important}.ml-xl-13,.mx-xl-13{margin-left:$s13 !important}";
                $xl .= ".p-xl-13{padding:$s13 !important}.{$pre}pt-xl-13,.py-xl-13{padding-top:$s13 !important}.pr-xl-13,.px-xl-13{padding-right:$s13 !important}.{$pre}pb-xl-13,.py-xl-13{padding-bottom:$s13 !important}.pl-xl-13,.px-xl-13{padding-left:$s13 !important}";
            }
            if ($s14) {
                $xl .= ".m-xl-14{margin:$s14 !important}.{$pre}mt-xl-14,.my-xl-14{margin-top:$s14 !important}.mr-xl-14,.mx-xl-14{margin-right:$s14 !important}.{$pre}mb-xl-14,.my-xl-14{margin-bottom:$s14 !important}.ml-xl-14,.mx-xl-14{margin-left:$s14 !important}";
                $xl .= ".p-xl-14{padding:$s14 !important}.{$pre}pt-xl-14,.py-xl-14{padding-top:$s14 !important}.pr-xl-14,.px-xl-14{padding-right:$s14 !important}.{$pre}pb-xl-14,.py-xl-14{padding-bottom:$s14 !important}.pl-xl-14,.px-xl-14{padding-left:$s14 !important}";
            }
            if ($s15) {
                $xl .= ".m-xl-15{margin:$s15 !important}.{$pre}mt-xl-15,.my-xl-15{margin-top:$s15 !important}.mr-xl-15,.mx-xl-15{margin-right:$s15 !important}.{$pre}mb-xl-15,.my-xl-15{margin-bottom:$s15 !important}.ml-xl-15,.mx-xl-15{margin-left:$s15 !important}";
                $xl .= ".p-xl-15{padding:$s15 !important}.{$pre}pt-xl-15,.py-xl-15{padding-top:$s15 !important}.pr-xl-15,.px-xl-15{padding-right:$s15 !important}.{$pre}pb-xl-15,.py-xl-15{padding-bottom:$s15 !important}.pl-xl-15,.px-xl-15{padding-left:$s15 !important}";
            }

            if ($xl) $styles .= "@media (min-width: 1280px){$xl}";
        }

        return $styles;
    }

    private function getDefault() {
        /*$this->load->model('setting/setting');
        $pg = $this->model_setting_setting->getSetting('pg');*/
        $pg = $this->registry->get('pgConfig');

        $container = isset($pg['container']) ? $pg['container'] : '';
        $container_sm = isset($pg['container_sm']) ? $pg['container_sm'] : '';
        $container_md = isset($pg['container_md']) ? $pg['container_md'] : '';
        $container_lg = isset($pg['container_lg']) ? $pg['container_lg'] : '';

        $heading_size = isset($pg['heading_size']) ? $pg['heading_size'] : '';
        $heading_margin = isset($pg['heading_margin']) ? $pg['heading_margin'] : '';
        $heading_line = isset($pg['heading_line']) ? $pg['heading_line'] : '';
        $heading_transform = isset($pg['heading_transform']) ? $pg['heading_transform'] : '';
        $title_size = isset($pg['title_size']) ? $pg['title_size'] : '';
        $title_margin = isset($pg['title_margin']) ? $pg['title_margin'] : '';
        $title_line = isset($pg['title_line']) ? $pg['title_line'] : '';
        $title_transform = isset($pg['title_transform']) ? $pg['title_transform'] : '';
        $description_size = isset($pg['description_size']) ? $pg['description_size'] : '';
        $description_margin = isset($pg['description_margin']) ? $pg['description_margin'] : '';
        $description_line = isset($pg['description_line']) ? $pg['description_line'] : '';
        $container_space_top = isset($pg['container_space_top']) ? $pg['container_space_top'] : '';
        $container_space_bottom = isset($pg['container_space_bottom']) ? $pg['container_space_bottom'] : '';

        $heading_size_mb = isset($pg['heading_size_mb']) ? $pg['heading_size_mb'] : '';
        $heading_margin_mb = isset($pg['heading_margin_mb']) ? $pg['heading_margin_mb'] : '';
        $heading_line_mb = isset($pg['heading_line_mb']) ? $pg['heading_line_mb'] : '';
        $heading_transform_mb = isset($pg['heading_transform_mb']) ? $pg['heading_transform_mb'] : '';
        $title_size_mb = isset($pg['title_size_mb']) ? $pg['title_size_mb'] : '';
        $title_margin_mb = isset($pg['title_margin_mb']) ? $pg['title_margin_mb'] : '';
        $title_line_mb = isset($pg['title_line_mb']) ? $pg['title_line_mb'] : '';
        $title_transform_mb = isset($pg['title_transform_mb']) ? $pg['title_transform_mb'] : '';
        $description_size_mb = isset($pg['description_size_mb']) ? $pg['description_size_mb'] : '';
        $description_margin_mb = isset($pg['description_margin_mb']) ? $pg['description_margin_mb'] : '';
        $description_line_mb = isset($pg['description_line_mb']) ? $pg['description_line_mb'] : '';
        $container_space_top_mb = isset($pg['container_space_top_mb']) ? $pg['container_space_top_mb'] : '';
        $container_space_bottom_mb = isset($pg['container_space_bottom_mb']) ? $pg['container_space_bottom_mb'] : '';

        $styles = '';
        $font_size_mb = 13;
        $root = '';
        if ($container) $root .= '--container: ' . $container . 'px;';
        if ($container_sm) $root .= '--container-sm: ' . $container_sm . 'px;';
        if ($container_md) $root .= '--container-md: ' . $container_md . 'px;';
        if ($container_lg) $root .= '--container-lg: ' . $container_lg . '%;';

        if ($heading_size_mb) $root .= '--heading-size: ' . $heading_size_mb / $font_size_mb . 'rem;';
        if ($heading_margin_mb) $root .= '--heading-margin: ' . $heading_margin_mb / $font_size_mb . 'rem;';
        if ($heading_line_mb) $root .= "--heading-line: $heading_line_mb;";
        if ($heading_transform_mb) $root .= "--heading-transform: $heading_transform_mb;";
        if ($title_size_mb) $root .= '--title-size: ' . $title_size_mb / $font_size_mb . 'rem;';
        if ($title_margin_mb) $root .= '--title-margin: ' . $title_margin_mb / $font_size_mb . 'rem;';
        if ($title_line_mb) $root .= "--title-line: $title_line_mb;";
        if ($title_transform_mb) $root .= "--title-transform: $title_transform_mb;";
        if ($description_size_mb) $root .= '--description-size: ' . $description_size_mb / $font_size_mb . 'rem;';
        if ($description_margin_mb) $root .= '--description-margin: ' . $description_margin_mb / $font_size_mb . 'rem;';
        if ($description_line_mb) $root .= "--description-line: $description_line_mb;";
        if ($container_space_top_mb) $root .= '--container-space-top: ' . $container_space_top_mb / $font_size_mb . 'rem;';
        if ($container_space_bottom_mb) $root .= '--container-space-bottom: ' . $container_space_bottom_mb / $font_size_mb . 'rem;';

        if ($root) $styles .= ":root{{$root}}";

        $font_size = 16;
        $xl = '';
        if ($heading_size) $xl .= '--heading-size: ' . $heading_size / $font_size . 'rem;';
        if ($heading_margin) $xl .= '--heading-margin: ' . $heading_margin / $font_size . 'rem;';
        if ($heading_line) $xl .= "--heading-line: $heading_line;";
        if ($heading_transform) $xl .= "--heading-transform: $heading_transform;";
        if ($title_size) $xl .= '--title-size: ' . $title_size / $font_size . 'rem;';
        if ($title_margin) $xl .= '--title-margin: ' . $title_margin / $font_size . 'rem;';
        if ($title_line) $xl .= "--title-line: $title_line;";
        if ($title_transform) $xl .= "--title-transform: $title_transform;";
        if ($description_size) $xl .= '--description-size: ' . $description_size / $font_size . 'rem;';
        if ($description_margin) $xl .= '--description-margin: ' . $description_margin / $font_size . 'rem;';
        if ($description_line) $xl .= "--description-line: $description_line;";
        if ($container_space_top) $xl .= '--container-space-top: ' . $container_space_top / $font_size . 'rem;';
        if ($container_space_bottom) $xl .= '--container-space-bottom: ' . $container_space_bottom / $font_size . 'rem;';

        if ($xl) $styles .= "@media (min-width: 1280px){:root{{$xl}}}";

        return $styles;
    }

    public function index() {
        $output = $this->getColorStyles();
//        $output .= $this->getSpacing();
        $output .= $this->getDefault();
        header('Content-type: text/css');
        echo $output;
        exit();
    }
}
