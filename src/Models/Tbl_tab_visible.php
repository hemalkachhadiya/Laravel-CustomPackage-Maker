<?php

// namespace App\Model;

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_tab_visible extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'tab_id', 'add_row', 'update_row', 'delete_row', 'excel', 'pdf', 'print', 'col_visible', 'show_row', 'visible',
    ];
}
