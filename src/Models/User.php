<?php

namespace Smarttech\Prod\Models;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // use HasApiTokens, Notifiable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'email', 'password', 'logo', 'company_name', 'country', 'state', 'city', 'pin', 'gstin', 'address', 'phone', 'currency', 'bank_name', 'branch_name', 'account_no', 'ifsc_no', 'remember_token', 'expire_date', 'ch_pass', 'datetime_formate', 'date_formate', 'show_signature', 'signature_logo', 'role_type', 'store_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
}
