<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Countrie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $table = 'tbl_order_status';

    protected $fillable = [
        'name', 'iso3', 'iso2', 'phonecode', 'capital', 'currency', 'flag',
    ];
}
