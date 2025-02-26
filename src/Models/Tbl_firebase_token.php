<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_firebase_token extends Model {
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */

    protected $fillable = [
        'user_id', 'firebase_token', 'onesignal_token'
    ];
}

