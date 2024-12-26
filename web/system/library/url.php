<?php
/**
 * @package        MotilaCore
 * @author        HuyD
 * @copyright    Copyright (c) 2018 - 2020
 * @link        https://motila.vn
 */

/**
 * URL class.
 */
class Url {
    /** @var string */
    private $url;

    /** @var Controller[] */
    private $rewrite = array();

    /**
     * Constructor.
     *
     * @param string $url
     * @param string $ssl Unused
     */
    public function __construct($url, $ssl = '') {
        $this->url = $url;
    }

    /**
     *
     *
     * @param Controller $rewrite
     *
     * @return void
     */
    public function addRewrite($rewrite) {
        $this->rewrite[] = $rewrite;
    }

    /**
     * @param string $route
     * @param string|string[] $args
     *
     * @return string
     */
    public function link($route, $args = '') {
        $url = $this->url . 'index.php?route=' . (string)$route;

        if ($args) {
            if (is_array($args)) {
                $url .= '&amp;' . http_build_query($args, '', '&amp;');
            } else {
                $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
            }
        }

        foreach ($this->rewrite as $rewrite) {
            $url = $rewrite->rewrite($url);
        }

        return $url;
    }

    /**
     * Plus Link
     *
     * @param string $url
     * @param string|string[] $args
     *
     * @return string
     */
    public function plus($url = '', $args = '') {
        $url = $this->url . trim($url, '/');

        if (strpos($url, '?') !== false) {
            $url = $url . '&';
        } else {
            $url = $url . '?';
        }

        if ($args) {
            if (is_array($args)) {
                $url .= http_build_query($args, '', '&amp;');
            } else {
                $url .= str_replace('&', '&amp;', ltrim($args, '&'));
            }
        }

        return rtrim(rtrim($url, '?'), '&');
    }
}
