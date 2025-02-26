<?php

// namespace App\Model;

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_tab extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'icon', 'url', 'status', 'position', 'type', 'tab_id',
    ];
}
