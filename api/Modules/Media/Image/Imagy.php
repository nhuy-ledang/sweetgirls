<?php namespace Modules\Media\Image;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Modules\Media\Entities\File;

class Imagy {
    /**
     * @var \Intervention\Image\Image
     */
    private $image;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;
    /**
     * @var ImageFactoryInterface
     */
    private $imageFactory;
    /**
     * @var ThumbnailsManager
     */
    private $manager;
    /**
     * All the different images types where thumbnails should be created
     * @var array
     */
    private $imageExtensions = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
    /**
     * @var Repository
     */
    private $config;

    /**
     * @param ImageFactoryInterface $imageFactory
     * @param ThumbnailsManager $manager
     * @param Repository $config
     */
    public function __construct(ImageFactoryInterface $imageFactory, ThumbnailsManager $manager, Repository $config) {
        $this->image = app('Intervention\Image\ImageManager');
        $this->finder = app('Illuminate\Filesystem\Filesystem');
        $this->imageFactory = $imageFactory;
        $this->manager = $manager;
        $this->config = $config;
    }

    /**
     * Get orientation data from image contents
     *
     * @param string $fileContents
     * @return int|null
     */
    private function getOrientation($fileContents) {
        $orientation = null;
        try {
            $exif = exif_read_data("data://image/jpeg;base64," . base64_encode($fileContents), 0, true);
            if ($exif) $orientation = Arr::get($exif, 'IFD0.Orientation', 0);
        } catch (\Exception $e) {
        }
        return $orientation;
    }

    /**
     * Perform image orientation
     *
     * @param int $orientation
     * @param \Intervention\Image\Image $image
     * @return \Intervention\Image\Image
     */
    private function getOrientatedImage($orientation, \Intervention\Image\Image $image) {
        switch ($orientation) {
            case 2:
                return $image->flip('h');
            case 3:
                return $image->rotate(180);
            case 4:
                return $image->rotate(180)->flip('h');
            case 5:
                return $image->rotate(-90)->flip('h');
            case 6:
                return $image->rotate(-90);
            case 7:
                return $image->rotate(-90)->flip('v');
            case 8:
                return $image->rotate(90);
        }

        return $image;
    }

    /**
     * Prepend the thumbnail name to filename
     *
     * @param string $path
     * @param string $thumbnail
     * @param string $filesPath
     * @return string
     */
    private function newFilename($path, $thumbnail, $filesPath = '') {
        $newFilename = rtrim(str_replace(($filesPath ? $filesPath : $this->config->get('media.config.files-path')), '', $path), '/');

        return $thumbnail . '/' . $newFilename;
    }

    /**
     * Return the already created file if it exists and force create is false
     *
     * @param  string $filename
     * @param  bool $forceCreate
     * @return bool
     */
    private function returnCreatedFile($filename, $forceCreate) {
        return $this->finder->isFile($filename) && !$forceCreate;
    }

    /**
     * Write the given image
     *
     * @param string $filename
     * @param string $image
     */
    private function writeImage($filename, $image) {
        if ($image instanceof \Intervention\Image\Image) $image = $image->getEncoded();
        \Storage::put(($filename), $image, "public");
        //@chmod(public_path($filename), 0666);
    }

    /**
     * Make a new image
     *
     * @param string $path
     * @param string $filename
     * @param string $thumbnail
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function makeNew($path, $filename, $thumbnail) {
        $image = $this->image->make(public_path() . $path);
        foreach ($this->manager->find($thumbnail) as $manipulation => $options) {
            $image = $this->imageFactory->make($manipulation)->handle($image, $options);
        }
        $image = $image->encode(pathinfo($path, PATHINFO_EXTENSION));
        $this->writeImage($filename, $image);
    }

    /**
     * Check if the given path is en image
     *
     * @param  string $path
     * @return bool
     */
    public function isImage($path) {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $this->imageExtensions);
    }

    /**
     * Get an image in the given thumbnail options
     *
     * @param string $path
     * @param string $thumbnail
     * @param bool $forceCreate
     * @return string|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function get($path, $thumbnail, $forceCreate = false) {
        if (!$this->isImage($path)) return;
        $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail);
        if ($this->returnCreatedFile($filename, $forceCreate)) return $filename;
        $this->makeNew($path, $filename, $thumbnail);

        return $filename;
    }

    /**
     * Return the thumbnail path
     *
     * @param $originalImage
     * @param $thumbnail
     * @param string $filesPath
     * @return string
     */
    public function getThumbnail($originalImage, $thumbnail, $filesPath = '') {
        if (!$this->isImage($originalImage)) return $originalImage;
        if (!$filesPath) {
            $filesPath = $this->config->get('media.config.files-path');
            $newFilesPath = substr($originalImage, 0, strlen($filesPath));
            if ($filesPath != $newFilesPath) {
                $temps = explode('/', ltrim($originalImage, '/'));
                $filesPath = '/' . $temps[0] . '/';
            }
        }

        return $filesPath . $this->newFilename($originalImage, $thumbnail, $filesPath);
    }

    /**
     * Insert watermark
     *
     * @param \Intervention\Image\Image $newImage
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @return \Intervention\Image\Image
     */
    private function insertWatermark(\Intervention\Image\Image $newImage, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10) {
        if ($has_watermark) {
            $width = $newImage->width();
            if ($width >= 900) {
                $filename = \Storage::disk('public')->exists('/watermark/watermark_lg_cus.png') ? 'watermark_lg_cus' : 'watermark_lg';
                $newImage->insert(public_path("watermark/$filename.png"), $position, $x, $y);
            } else if ($width >= 512) {
                $filename = \Storage::disk('public')->exists('/watermark/watermark_md_cus.png') ? 'watermark_md_cus' : 'watermark_md';
                $newImage->insert(public_path("watermark/$filename.png"), $position, $x, $y);
            } else {
                $filename = \Storage::disk('public')->exists('/watermark/watermark_sm_cus.png') ? 'watermark_sm_cus' : 'watermark_sm';
                $newImage->insert(public_path("watermark/$filename.png"), $position, $x, $y);
            }
        }
        return $newImage;
    }

    /**
     * Create all thumbnails for the given image path
     *
     * @param string $path
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createAll($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        foreach ($this->manager->all() as $thumbnail) {
            if ($is_origin) {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name());
            } else {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail->name());
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, $options);
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Create all thumbnails for the given image path
     *
     * @param string $path
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createAll_1by1($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        foreach ($this->manager->all_1by1() as $thumbnail) {
            if ($is_origin) {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name());
            } else {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail->name());
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, $options);
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Create all thumbnails for the given image path
     *
     * @param string $path
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createAll_3by4($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        foreach ($this->manager->all_3by4() as $thumbnail) {
            if ($is_origin) {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name());
            } else {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail->name());
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, $options);
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Create all thumbnails for the given image path
     *
     * @param string $path
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createAll_4by3($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        foreach ($this->manager->all_4by3() as $thumbnail) {
            if ($is_origin) {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name());
            } else {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail->name());
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, $options);
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Create all thumbnails for the given image path
     *
     * @@param string $path
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createAll_9by16($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        foreach ($this->manager->all_9by16() as $thumbnail) {
            if ($is_origin) {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name());
            } else {
                $filename = $this->config->get('media.config.files-path') . $this->newFilename($path, $thumbnail->name());
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, $options);
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Delete all files on disk for the given file in storage
     * This means the original and the thumbnails
     *
     * @param $file
     * @return bool
     */
    public function deleteAllFor(File $file) {
        if (!$this->isImage($file->path)) return $this->finder->delete($file->path);
        $paths = [];
        if (\Storage::exists($file->path)) $paths[] = $file->path;
        foreach ($this->manager->all() as $thumbnail) {
            $filename = $this->config->get('media.config.files-path') . $this->newFilename(str_replace('/origin/', '/', $file->path), $thumbnail->name());
            if (\Storage::exists($filename)) $paths[] = $filename;
            $filename = $this->config->get('media.config.files-path') . $this->newFilename($file->path, $thumbnail->name());
            if (\Storage::exists($filename)) $paths[] = $filename;
        }

        return \Storage::delete($paths);
    }

    /**
     * Create thumbnails for default
     * @param string $path
     * @param string $filesPath
     * @param boolean $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @param boolean $is_origin
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createDefaultThumbs($path, $filesPath = '', $has_watermark = false, $position = 'top-right', $x = 10, $y = 10, $is_origin = false) {
        if (!$this->isImage($path)) return;
        $filesPath = $filesPath ? $filesPath : $this->config->get('media.config.files-path');
        $file = \Storage::get($path);
        $orientation = $this->getOrientation($file);
        $image = $this->image->make($file);
        $width = $image->getWidth();
        $height = $image->getHeight();
        $aspectRatio = $width / $height;
        $small = 200;
        $thumb = 400;
        $large = 800;
        if ($width < $small) $small = $width;
        if ($width < $thumb) $thumb = $width;
        if ($width < $large) $large = $width;
        $configuration = [
            'small' => ['resize' => ['width' => $small, 'height' => round($small / $aspectRatio, 2), 'aspectRatio' => true, 'upsize' => true]],
            'thumb' => ['resize' => ['width' => $thumb, 'height' => round($thumb / $aspectRatio, 2), 'aspectRatio' => true, 'upsize' => true]],
            'large' => ['resize' => ['width' => $large, 'height' => round($large / $aspectRatio, 2), 'aspectRatio' => true, 'upsize' => true]],
        ];
        $thumbnails = array_merge([], Thumbnail::makeMultiple($configuration));
        foreach ($thumbnails as $thumbnail) {
            if ($is_origin) {
                $filename = $filesPath . $this->newFilename(str_replace('/origin/', '/', $path), $thumbnail->name(), $filesPath);
            } else {
                $filename = $filesPath . $this->newFilename($path, $thumbnail->name(), $filesPath);
            }
            $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
            foreach ($thumbnail->filters() as $manipulation => $options) {
                $newImage = $this->imageFactory->make($manipulation)->handle($newImage, array_merge($options, ['orientation' => $orientation]));
            }
            $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
            $newImage = $newImage->encode(pathinfo($path, PATHINFO_EXTENSION));
            $this->writeImage($filename, $newImage);
        }
    }

    /**
     * Delete all files on disk for the given file in storage
     * This means the original and the thumbnails
     *
     * @param string $path
     * @param string $filesPath
     * @return bool
     */
    public function deleteDefaultThumbs($path, $filesPath = '') {
        if (!$this->isImage($path)) return $this->finder->delete($path);
        $filesPath = $filesPath ? $filesPath : $this->config->get('media.config.files-path');
        $paths = [];
        if (\Storage::exists($path)) $paths[] = $path;
        foreach ($this->manager->all() as $thumbnail) {
            $filename = $filesPath . $this->newFilename($path, $thumbnail->name(), $filesPath);
            if (\Storage::exists($filename)) $paths[] = $filename;
        }

        return \Storage::delete($paths);
    }

    /**
     * @param $path
     * @param bool $has_watermark
     * @param string $position
     * @param integer $x
     * @param integer $y
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function generateWatermark($path, $has_watermark = false, $position = 'top-right', $x = 10, $y = 10) {
        if (!$this->isImage($path)) return;
        $configFilePath = $this->config->get('media.config.files-path');
        $originFilePath = str_replace(rtrim($configFilePath, '/'), rtrim($configFilePath, '/') . '/origin', $path);
        if (!\Storage::exists($originFilePath)) \Storage::copy($path, $originFilePath);

        $file = \Storage::get($originFilePath);
        $image = $this->image->make($file);
        $width = $image->getWidth();
        $height = $image->getHeight();
        $aspectRatio = $width / $height;

        // Insert watermark to thumb
        if ($aspectRatio == 0.56) { //9:16
            $this->createAll_9by16($originFilePath, $has_watermark, $position, $x, $y, true);
        } else if ($aspectRatio == 1.33) { //4:3
            $this->createAll_4by3($originFilePath, $has_watermark, $position, $x, $y, true);
        } else if ($aspectRatio == 0.75) { //3:4
            $this->createAll_3by4($originFilePath, $has_watermark, $position, $x, $y, true);
        } else if ($aspectRatio == 1) { //1:1
            $this->createAll_1by1($originFilePath, $has_watermark, $position, $x, $y, true);
        } else if ($aspectRatio == 1.78) { //16:9
            $this->createAll($originFilePath, $has_watermark, $position, $x, $y, true);
        } else {
            $this->createDefaultThumbs($originFilePath, '', $has_watermark, $position, $x, $y, true);
        }

        // Insert watermark to origin image
        //if ($has_watermark) {
        $orientation = $this->getOrientation($file);
        $newImage = $this->getOrientatedImage($orientation, $this->image->make($file));
        $this->insertWatermark($newImage, $has_watermark, $position, $x, $y);
        $newImage = $newImage->encode(pathinfo($originFilePath, PATHINFO_EXTENSION));
        $this->writeImage($path, $newImage);
        //} else {
        //    //\Storage::copy($originFilePath, $path);
        //    \Storage::put($path, $image, "public");
        //}
    }
}
