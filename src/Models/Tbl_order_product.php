<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_order_product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pro_id',
        'order_id',
        'shipping_price',
        'user_id',
        'pro_name',
        'pro_sku',
        'pro_description',
        'pro_mrp_price',
        'pro_discount_value',
        'pro_discount_price',
        'pro_price',
        'pro_qty',
        'total_amount',
        'option',
        'image_id',
        'prescription_required',
        'confirm',
        'pro_tax',
        'manu_id',
        'batch_no',
        'exp_date',
        'uom_unit',
        'hsn',
    ];

    public function orders()
    {
        return $this->belongsTo(Tbl_order::class, 'order_id');
    }
}
