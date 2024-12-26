<?php

/**
 * Breadcrumbs class
 */
class Breadcrumbs {
    private $list = array();

    public function addBreadcrumb($text, $href) {
        $this->list[] = array(
            'text' => $text,
            'href' => $href,
        );
    }

    public function render() {
        $output = '';

        if (!empty($this->list)) {
            $output .= '<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
            $step = 1;
            foreach ($this->list as $breadcrumb) {
                $output .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                $output .= '  <a itemprop="item" href="' . $breadcrumb['href'] . '"><span itemprop="name">' . $breadcrumb['text'] . '</span></a>';
                $output .= '  <meta itemprop="position" content="' . $step++ . '" />';
                $output .= '</li>';
            }
            $output .= '</ul>';
        }

        return $output;
    }

    private function renderToJson() {
        $output = "";
        if (!empty($this->list)) {
            $output .= "<script type=\"application/ld+json\">";
            $output .= "\n{";
            $output .= "\n  \"@context\": \"http://schema.org\",";
            $output .= "\n  \"@type\": \"BreadcrumbList\",";
            $output .= "\n  \"itemListElement\": [";
            $step = 1;
            foreach ($this->list as $breadcrumb) {
                $output .= "\n    {";
                $output .= "\n      \"@type\": \"ListItem\",";
                $output .= "\n      \"position\": " . $step++ . ",";
                $output .= "\n      \"item\": {";
                $output .= "\n        \"@id\": \"" . $breadcrumb['href'] . "\",";
                $output .= "\n        \"name\": \"" . $breadcrumb['text'] . "\"";
                $output .= "\n      }";
                $output .= "\n    }";
                if ($step - 1 < count($this->list)) {
                    $output .= ",";
                }
            }
            $output .= "\n  ]";
            $output .= "\n}";
            $output .= "\n</script>\n";
        }

        echo $output;
    }

    public function getJson() {
        ob_start();

        $this->renderToJson();

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }
}