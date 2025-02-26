<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_order_bill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'first_name',
        'last_name',
        'company_name',
        'address',
        'country',
        'state',
        'city',
        'pin',
        'phone',
        'alternativ_phone',
        'landmark',
        'lat',
        'lang'
    ];
}
