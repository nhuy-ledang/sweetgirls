<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class TicketFile
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class TicketFile extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__ticket_files';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['owner_id', 'ticket_id', 'type', 'filename', 'path', 'extension', 'mimetype', 'filesize'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'owner_id'  => 'integer',
        'ticket_id' => 'integer',
        'filesize'  => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    public function getUrlAttribute() {
        return media_url_file($this->path);
    }

    /**
     * Relationship
     */
    public function owner() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'owner_id', 'id')->withTrashed();
    }

    public function ticket() {
        return $this->belongsTo('\Modules\Stock\Entities\Ticket', 'ticket_id', 'id');
    }
}
