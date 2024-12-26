<?php namespace Modules\Media\Repositories\Eloquent;

use Imagy;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Media\Entities\File;
use Modules\Media\Helpers\FileHelper;
use Modules\Media\Repositories\FileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EloquentFileRepository extends EloquentBaseRepository implements FileRepository {
    /**
     * Update a resource
     * @param  File $file
     * @param $data
     * @return mixed
     */
    public function update($file, $data) {
        if (!empty($data['slug']) && $this->model->where('slug', $data['slug'])->where("id", "<>", $file->getKey())->first()) {
            return false;
        } else {
            if (!empty($data['slug'])) $data['slug'] = str_slug($data['slug']);
            $file->fill($data)->save();
            //$file->update($data);
            return $file;
        }
    }

    /**
     * Create a file row from the given file
     * @param UploadedFile $file
     * @param array $optional
     * @return mixed
     */
    public function createFromFile(UploadedFile $file, $optional = []) {
        $fileName = !isset($optional['filename']) ? FileHelper::slug($file->getClientOriginalName()) : $optional['filename'];
        $path = !isset($optional['path']) ? config('media.config.files-path') . "{$fileName}" : $optional['path'];
        /*$exists = $this->model->whereFilename($fileName)->first();
        if ($exists) throw new \InvalidArgumentException('File slug already exists');*/
        $mimeType = $file->getClientMimeType();
        $data = [
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'extension' => $file->guessClientExtension(),
            'mimetype'  => $mimeType,
            'filesize'  => $file->getFileInfo()->getSize(),
        ];
        list($type, $subtype) = explode('/', $mimeType);
        if ($type == 'image' && in_array($subtype, ['gif', 'png', 'jpeg'])) {
            $info = getimagesize($file);
            $width = $info[0];
            $height = $info[1];
            $data['width'] = $width;
            $data['height'] = $height;
        }
        if (!empty($optional['user_id'])) $data['user_id'] = $optional['user_id'];
        if (!empty($optional['sub'])) $data['object'] = $optional['sub'];
        if (!empty($optional['object_id'])) $data['object_id'] = $optional['object_id'];
        if (!empty($optional['folder_id'])) $data['folder_id'] = $optional['folder_id'];

        return $this->model->create($data);
    }

    public function destroy($file) {
        Imagy::deleteAllFor($file);
        $file->delete();
    }

    public function fileBySlug($slug) {
        return $this->getModel()->where('slug', $slug)->first();
    }


}
