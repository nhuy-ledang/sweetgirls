<?php

/**
 * Document class
 */
class Document {
    private $title;
    private $description;
    private $keywords;
    private $image;
    private $image_alt;
    private $audio;
    private $video;
    private $price;
    private $links = [];
    private $styles = [];
    private $scripts = [];
    private $scriptCodes = [];
    private $cssCodes = [];
    private $metaTags = [];

    /**
     * @param    string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     *
     *
     * @return    string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @param   string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     *
     * @return  string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     *
     *
     * @param   string $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    /**
     *
     * @return  string
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     *
     * @param   string $href
     * @param   string $rel
     */
    public function addLink($href, $rel) {
        $this->links[$href] = ['href' => $href, 'rel' => $rel];
    }

    /**
     * @return    array
     */
    public function getLinks() {
        return $this->links;
    }

    /**
     * @param    string $href
     * @param    string $rel
     * @param    string $media
     */
    public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
        $this->styles[$href] = ['href' => $href, 'rel' => $rel, 'media' => $media];
    }

    /**
     *
     * @return    array
     */
    public function getStyles() {
        return $this->styles;
    }

    /**
     *
     * @param    string $href
     * @param    string $position
     */
    public function addScript($href, $position = 'header') {
        $this->scripts[$position][$href] = $href;
    }

    /**
     *
     *
     * @param   string $position
     *
     * @return  array
     */
    public function getScripts($position = 'header') {
        if (isset($this->scripts[$position])) {
            return $this->scripts[$position];
        } else {
            return [];
        }
    }

    /**
     *
     * @param   $code
     */
    public function addScriptCode($code) {
        $this->scriptCodes[] = $code;
    }

    /**
     *
     * @return array
     */
    public function getScriptCodes() {
        return $this->scriptCodes;
    }

    /**
     *
     * @param $code
     */
    public function addCssCode($code) {
        $this->cssCodes[] = $code;
    }

    /**
     *
     * @return array
     */
    public function getCssCodes() {
        return $this->cssCodes;
    }

    /**
     *
     * @param $property
     * @param $content
     */
    public function addMetaTag($property, $content) {
        $this->metaTags[] = ['property' => $property, 'content' => $content];
    }

    /**
     *
     * @return array
     */
    public function getMetaTags() {
        return $this->metaTags;
    }

    /**
     *
     * @param $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     *
     * @return mixed
     */
    public function getImage() {
        return $this->image;
    }

    /**
     *
     * @param $image_alt
     */
    public function setImageAlt($image_alt) {
        $this->image_alt = $image_alt;
    }

    /**
     *
     * @return mixed
     */
    public function getImageAlt() {
        return $this->image_alt;
    }

    /**
     *
     * @param $audio
     */
    public function setAudio($audio) {
        $this->audio = $audio;
    }

    /**
     *
     * @return mixed
     */
    public function getAudio() {
        return $this->audio;
    }

    /**
     *
     * @param $video
     */
    public function setVideo($video) {
        $this->video = $video;
    }

    /**
     *
     * @return mixed
     */
    public function getVideo() {
        return $this->video;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     *
     * @return mixed
     */
    public function getPrice() {
        return $this->price;
    }
}
