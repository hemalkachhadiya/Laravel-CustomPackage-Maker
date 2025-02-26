<?php

// namespace App\Model;

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Table_visible extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'notvisible',
    ];
}
