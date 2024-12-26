<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height, $crop = false, $crop_type = "c") {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;
        $image_new = 'cache/' . (int)$width . 'x' . (int)$height . '/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
				return $this->config->get('config_url') . 'image/' . $image_old;
			}
						
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
                if ($crop == false) {
                    $image->resize($width, $height);
                } else {
                    $image->resizeCrop($width, $height, $crop_type);
                }
                //$image->watermark(new Image(DIR_IMAGE . 'watermark.png'), 'bottomright', 30, 30);
                $image->save(DIR_IMAGE . $image_new);
			} else {
			    //$image = new Image(DIR_IMAGE . $image_old);
                //$image->watermark(new Image(DIR_IMAGE . 'watermark.png'), 'bottomright', 30, 30);
                //$image->save(DIR_IMAGE . $image_new);
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		
		return $this->config->get('config_url') . 'image/' . $image_new;
	}

	public function getUrl($filename) {
        return $this->config->get('config_url') . 'image/' . $filename;
    }

    public function getThumbnail($path, $thumbnail = '') {
        return media_url_file($path, $thumbnail);
    }
}
