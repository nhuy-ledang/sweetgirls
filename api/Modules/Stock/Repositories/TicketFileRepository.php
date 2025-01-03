<?php namespace Modules\Stock\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface TicketFileRepository extends BaseRepository {
    /**
     * Create a file row from the given file
     * @param  UploadedFile $file
     * @return mixed
     */
    public function createFromFile(UploadedFile $file);
}
