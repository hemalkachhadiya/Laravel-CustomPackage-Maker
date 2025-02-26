<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_salt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'side_effect', 'indication', 'contra_indication', 'caution',
    ];
}
