<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Contact extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'contact__contacts';

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'company',
        'company',
        'phone',
        'website',
        'message',
        'categories',
        'type',
        'gender',
        'file',
    ];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'file_url'
    ];

    public function getFileUrlAttribute() {
        return config('filesystems.disks.local.url') . $this->file;
    }
}
