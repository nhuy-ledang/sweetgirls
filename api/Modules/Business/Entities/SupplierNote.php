<?php namespace Modules\Business\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class SupplierNote
 *
 * @package Modules\Business\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class SupplierNote extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sup__supplier_notes';

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
    protected $fillable = ['supplier_id', 'owner_id', 'note'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'supplier_id' => 'integer',
        'owner_id'    => 'integer',
    ];

    /**
     * Relationship
     */
    public function supplier() {
        return $this->belongsTo('\Modules\Business\Entities\Supplier', 'supplier_id', 'id')->withTrashed();
    }

    public function owner() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'owner_id', 'id')->withTrashed();
    }
}
