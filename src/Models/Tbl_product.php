<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_product extends Model{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'hsn_code',
        'name',
        'medicine_name',
        'slug',
        'generic_name',
        'manufature',
        'batch_no',
        'gst',
        'discount',
        'video_url',
        'expiration_date',
        'prescription_required',
        'description',
        'categories',
        'shipping_charge',
        'tax_class',
        'status',
        'price',
        'mrp_price',
        'special_price',
        'special_start_price',
        'special_end_price',
        'sku',
        'inventory_manage',
        'qty',
        'stock_manage',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'related_product',
        'up_sell',
        'cross_sell',
        'short_description',
        'product_new_from',
        'product_new_to',
        'like_count',
        'unit',
        'package_per_unit',
        'price_per_unit',
        'product_form',
        'cat',
        'test_id',
        'is_drug',
        'pack_size_label'
    ];


}
