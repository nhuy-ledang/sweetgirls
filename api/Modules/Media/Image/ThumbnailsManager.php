<?php namespace Modules\Media\Image;

use Illuminate\Contracts\Config\Repository;

class ThumbnailsManager {
    /**
     * @var Repository
     */
    private $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config) {
        $this->config = $config;
    }

    /**
     * Return all thumbnails for all modules
     * @return array
     */
    public function all() {
        $thumbnails = [];

        $configuration = $this->config->get('media.thumbnails');
        if (!is_null($configuration)) {
            $thumbnails = array_merge($thumbnails, Thumbnail::makeMultiple($configuration));
        }

        return $thumbnails;
    }

    /**
     * Return all thumbnails for all modules
     * @return array
     */
    public function all_1by1() {
        $thumbnails = [];

        $configuration = $this->config->get('media.thumbnails_1by1');
        if (!is_null($configuration)) {
            $thumbnails = array_merge($thumbnails, Thumbnail::makeMultiple($configuration));
        }

        return $thumbnails;
    }

    /**
     * Return all thumbnails for all modules
     * @return array
     */
    public function all_3by4() {
        $thumbnails = [];

        $configuration = $this->config->get('media.thumbnails_3by4');
        if (!is_null($configuration)) {
            $thumbnails = array_merge($thumbnails, Thumbnail::makeMultiple($configuration));
        }

        return $thumbnails;
    }

    /**
     * Return all thumbnails for all modules
     * @return array
     */
    public function all_4by3() {
        $thumbnails = [];

        $configuration = $this->config->get('media.thumbnails_4by3');
        if (!is_null($configuration)) {
            $thumbnails = array_merge($thumbnails, Thumbnail::makeMultiple($configuration));
        }

        return $thumbnails;
    }

    /**
     * Return all thumbnails for all modules
     * @return array
     */
    public function all_9by16() {
        $thumbnails = [];

        $configuration = $this->config->get('media.thumbnails_9by16');
        if (!is_null($configuration)) {
            $thumbnails = array_merge($thumbnails, Thumbnail::makeMultiple($configuration));
        }

        return $thumbnails;
    }

    /**
     * Find the filters for the given thumbnail
     * @param $thumbnail
     * @return array
     */
    public function find($thumbnail) {
        foreach ($this->all() as $thumb) {
            if ($thumb->name() == $thumbnail) {
                return $thumb->filters();
            }
        }

        return [];
    }
}
