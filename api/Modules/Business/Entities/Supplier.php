<?php namespace Modules\Business\Entities;

use Imagy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Supplier
 *
 * @package Modules\Business\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Supplier extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sup__suppliers';

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
    protected $fillable = ['idx', 'supplier_type', 'group_id', 'category_id', 'contact_id', 'email', 'phone_number', 'fullname', 'bank_number', 'bank_name', 'card_holder', 'company', 'company_phone', 'company_email', 'company_bank_number', 'address', 'tax', 'website', 'note', 'description', 'image', 'status'];

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
    protected $appends = ['thumb_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'group_id'    => 'integer',
        'category_id' => 'integer',
        'contact_id'  => 'integer',
        'status'      => 'boolean',
    ];

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->image)) $hidden = array_merge($hidden, ['thumb_url']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function category() {
        return $this->belongsTo('\Modules\Business\Entities\SupplierCategory', 'category_id', 'id');
    }

    public function group() {
        return $this->belongsTo('\Modules\Business\Entities\SupplierGroup', 'group_id', 'id');
    }
}
