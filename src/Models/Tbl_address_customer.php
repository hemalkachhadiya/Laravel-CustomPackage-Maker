<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_address_customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'type',
        'country',
        'country_id',
        'state',
        'state_id',
        'city',
        'city_id',
        'zip',
        'alternativ_phone',
        'landmark',
        'lat',
        'lang',
        'defult',
    ];
}
