<?php namespace Modules\Media\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FolderRepository extends BaseRepository {
    /**
     * @param $id
     * @return string
     */
    public function getPath($id);
}
