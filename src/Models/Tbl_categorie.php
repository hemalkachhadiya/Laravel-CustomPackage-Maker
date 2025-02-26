<?php

// namespace App\Model;

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_categorie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'slug', 'searchable', 'image', 'status', 'seo', 'type', 'categories_id', 'level',
    ];
}
