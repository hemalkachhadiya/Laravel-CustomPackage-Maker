<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'code', 'discount_type', 'value', 'start_date', 'end_date', 'status', 'minimum_cart_amount', 'maximun_spend', 'limit_use_per_person',
    ];
}
