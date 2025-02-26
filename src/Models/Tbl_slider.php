<?php

namespace Smarttech\Prod\Models;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tbl_slider extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'logo', 'link', 'is_active', 'type', 'category',
    ];
}
