<?php namespace Modules\Media\Entities;

use Imagy;

class File extends FileBase {

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['object', 'object_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['type', 'subtype', 'raw_url', 'large_url', 'thumb_url', 'small_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'filesize'  => 'double',
        'width'     => 'double',
        'height'    => 'double',
        'watermark' => 'boolean',
        'x'         => 'integer',
        'y'         => 'integer',
    ];

    public function getTypeAttribute() {
        list($type, $subtype) = explode('/', $this->mimetype);
        return $type;
    }

    public function getSubtypeAttribute() {
        list($type, $subtype) = explode('/', $this->mimetype);
        return $subtype;
    }

    public function getRawUrlAttribute() {
        return media_url_file($this->path) . '?v=' . time();
    }

    public function getLargeUrlAttribute() {
        if (in_array($this->extension, ['gif', 'svg', 'mp4'])) {
            return media_url_file($this->path) . '?v=' . time();
        } else {
            return media_url_file(Imagy::getThumbnail($this->path, 'large')) . '?v=' . time();
        }
    }

    public function getThumbUrlAttribute() {
        if (in_array($this->extension, ['gif', 'svg', 'mp4'])) {
            return media_url_file($this->path) . '?v=' . time();
        } else {
            return media_url_file(Imagy::getThumbnail($this->path, 'thumb')) . '?v=' . time();
        }
    }

    public function getSmallUrlAttribute() {
        if (in_array($this->extension, ['gif', 'svg', 'mp4'])) {
            return media_url_file($this->path) . '?v=' . time();
        } else {
            return media_url_file(Imagy::getThumbnail($this->path, 'small')) . '?v=' . time();
        }
    }
}
