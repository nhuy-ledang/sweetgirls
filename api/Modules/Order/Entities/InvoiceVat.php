<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class InvoiceVat
 *
 * @package Modules\Order\Entities

 
 */
class InvoiceVat extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__invoice_vats';

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
    protected $fillable = ['invoice_id', 'id_attr', 'no', 'date_release', 'code_cqt', 'serial', 'lookup_code', 'domain_lookup', 'history', 'type', 'vat_amount', 'total', 'amount'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'invoice_id' => 'integer',
        'history'    => 'json',
    ];

    /**
     * Relationship
     */
    /*public function detail() {
        return $this->hasMany('\Modules\Order\Entities\InvoiceVatDetail', 'invoice_vat_id', 'id')->orderBy('num', 'asc');
    }*/
}
