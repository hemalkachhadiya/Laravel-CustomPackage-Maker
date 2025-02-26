<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Citie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $table = 'tbl_order_status';

    protected $fillable = [
        'name', 'state_id', 'country_id',
    ];
}
