<?php namespace Modules\Media\Image\Intervention\Manipulations;

use Modules\Media\Image\ImageHandlerInterface;

class Resize implements ImageHandlerInterface {
    private $defaults = [
        'width'       => 200,
        'height'      => 200,
        'aspectRatio' => true,
        'upsize'      => true,
    ];

    /**
     * Handle the image manipulation request
     * @param  \Intervention\Image\Image $image
     * @param  array $options
     * @return \Intervention\Image\Image
     */
    public function handle($image, $options) {
        $options = array_merge($this->defaults, $options);
        $width = $options['width'];
        $height = null;
        if ($options['width'] && $options['height']) {
            $w = $image->getWidth();
            $h = $image->getHeight();
            if ($w / $options['width'] > $h / $options['height']) {
                $width = $options['width'];
                $height = null;
            } else {
                $width = null;
                $height = $options['height'];
            }
        }
        $image->resize($width, $height, function($constraint) use ($options) {
            if ($options['aspectRatio']) $constraint->aspectRatio();
            if ($options['upsize']) $constraint->upsize();
        });
        if ($options['width'] && $options['height']) {
            $image->resizeCanvas((int)$options['width'], (int)$options['height'], 'center', false, [255, 255, 255, 1]);
            //$image->crop($options['width'], $options['height']);
        }

        return $image;
    }
}
