<?php namespace Modules\Media\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class FileBase extends CoreModel {
    protected $table = 'media__files';

    protected $fillable = ['user_id', 'object', 'object_id', 'folder_id', 'filename', 'path', 'extension', 'mimetype', 'filesize', 'width', 'height', 'watermark', 'position', 'x', 'y'];
}
