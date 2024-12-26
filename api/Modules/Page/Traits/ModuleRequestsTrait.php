<?php namespace Modules\Page\Traits;

use Imagy;

/**
 * Trait ModuleResultModelTrait
 *
 * @package Modules\Page\Traits
 */
trait ModuleRequestsTrait {
    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return mixed
     */
    public function uploadAttachFile(\Illuminate\Http\UploadedFile $file) {
        $mimeType = $file->getClientMimeType();
        // Save file to storage
        $path = '/attaches/' . date('Y') . '/' . date('md') . "/" . \Modules\Media\Helpers\FileHelper::slug($file->getClientOriginalName());
        $ok = \Storage::put($path, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $mimeType]);
        // Save the file info to db
        if ($ok) {
            return [
                'original_name' => $file->getClientOriginalName(),
                'path'          => $path,
                'extension'     => strtolower($file->guessClientExtension()),
                'mimetype'      => $mimeType,
                'filesize'      => $file->getFileInfo()->getSize(),
            ];
        }
        return null;
    }

    public function getRequestProperties() {
        $properties = $this->request->get('properties');
        if ($properties) {
            if (is_string($properties)) $properties = json_decode($properties, true);
            $file_path = $this->request->get("prop_filepath");
            if ($file_path) {
                $properties['bgImg'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile("prop_file");
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                    if (!is_string($savedFile)) $properties['bgImg'] = $savedFile->path;
                }
            }
        }

        return $properties ? $properties : [];
    }

    public function getRequestTableContents() {
        $table_contents = $this->request->get('table_contents');
        if ($table_contents) {
            if (is_string($table_contents)) $table_contents = json_decode($table_contents, true);
        }
        if (!empty($table_contents)) {
            $newVal = [];
            foreach ($table_contents as $k => $value) {
                $file_path = $this->request->get("filepath_$k");
                if ($file_path) {
                    $value['image'] = $file_path;
                } else {
                    list($file, $errorKey) = $this->getRequestFile("file_$k");
                    if ($file) {
                        $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                        if (!is_string($savedFile)) $value['image'] = $savedFile->path;
                    }
                }
                $file_path2 = $this->request->get("filepath2_$k");
                if ($file_path2) {
                    $value['image2'] = $file_path2;
                } else {
                    list($file2, $errorKey) = $this->getRequestFile("file2_$k");
                    if ($file2) {
                        $savedFile = $this->fileService->store($file2, ['sub' => trim($this->module_prefix, '_')]);
                        if (!is_string($savedFile)) $value['image2'] = $savedFile->path;
                    }
                }
                $file_path3 = $this->request->get("filepath3_$k");
                if ($file_path3) {
                    $value['image3'] = $file_path3;
                } else {
                    list($file3, $errorKey) = $this->getRequestFile("file3_$k");
                    if ($file3) {
                        $savedFile = $this->fileService->store($file3, ['sub' => trim($this->module_prefix, '_')]);
                        if (!is_string($savedFile)) $value['image3'] = $savedFile->path;
                    }
                }
                $attach_file = $this->request->file("attache_$k");
                if ($attach_file) {
                    $attachData = $this->uploadAttachFile($attach_file);
                    if ($attachData) $value['attach'] = $attachData['path'];
                }
                $newVal[] = $value;
            }
            $table_contents = $newVal;
        }

        return $table_contents ? $table_contents : [];
    }

    protected function getRequestTableImages() {
        $table_images = $this->request->get('table_images');
        if ($table_images) {
            if (is_string($table_images)) $table_images = json_decode($table_images, true);
        }
        if (!empty($table_images)) {
            $newVal = [];
            foreach ($table_images as $k => $value) {
                $img_path = $this->request->get("imgpath_$k");
                if ($img_path) {
                    $value['image'] = $img_path;
                } else {
                    list($img, $errorKey) = $this->getRequestFile("img_$k");
                    if ($img) {
                        $savedFile = $this->fileService->store($img, ['sub' => trim($this->module_prefix, '_')]);
                        if (!is_string($savedFile)) $value['image'] = $savedFile->path;
                    }
                }
                $newVal[] = $value;
            }
            $table_images = $newVal;
        }

        return $table_images ? $table_images : [];
    }
}
