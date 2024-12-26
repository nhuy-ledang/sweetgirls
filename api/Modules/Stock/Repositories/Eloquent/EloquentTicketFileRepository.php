<?php namespace Modules\Stock\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Modules\Stock\Repositories\TicketFileRepository;

class EloquentTicketFileRepository extends EloquentBaseRepository implements TicketFileRepository {
    /**
     * Create a file row from the given file
     * @param UploadedFile $file
     * @param array $optional
     * @return mixed
     */
    public function createFromFile(UploadedFile $file, $optional = []) {
        $filename = empty($optional['filename']) ? \Modules\Media\Helpers\FileHelper::slug($file->getClientOriginalName()) : $optional['filename'];
        $mimeType = $file->getClientMimeType();
        $path = "/stocks/tickets/{$optional['ticket_id']}-" . time() . "-$filename";
        $ok = \Storage::disk(config('filesystems.default'))->getDriver()->put($path, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $mimeType]);
        if (!$ok) return ("Can not upload file to service");
        $filename = empty($optional['filename']) ? $file->getClientOriginalName() : $optional['filename'];
        $data = [
            'filename'  => $filename,
            'path'      => $path,
            'extension' => strtolower($file->guessClientExtension()),
            'mimetype'  => $file->getClientMimeType(),
            'filesize'  => $file->getFileInfo()->getSize(),
        ];
        return $this->model->create(array_merge($optional, $data));
    }

    public function destroy($model) {
        if ($model->path && \Storage::exists($model->path)) \Storage::delete($model->path);
        parent::destroy($model);
    }
}
