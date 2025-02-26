<?php

namespace Smarttech\Prod\Models;

// use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tbl_customer extends Authenticatable
{
    // use HasApiTokens, Notifiable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guard = 'front';

    protected $fillable = [
        'first_name',
        'user_id',
        'last_name',
        'phone',
        'email',
        'currency',
        'address',
        'country',
        'city',
        'state',
        'pin',
        'logo',
        'password',
        'gender',
        'birth_date',
        'api_token',
        'otp',
        'id_proof',
        'authenticated',
        'ch_password',
        'device_token',
        'device_type',
        'onesignal_token',
        'register_type',
        'referal_code',
        'referal_from',
        'wallet_balance',
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
    public static function get_details($id)
    {
        $jsonArray = [];
        $user = Tbl_customer::where('id', '=', $id)->first();
        if (isset($user)) {
            $jsonObject['id'] = $user->id;
            $jsonObject['first_name'] = ($user->first_name != null) ? $user->first_name : '';
            $jsonObject['last_name'] = ($user->last_name != null) ? $user->last_name : ''; // $user->last_name;
            $jsonObject['email'] = ($user->email != null) ? $user->email : ''; // $user->email;
            $jsonObject['phone'] = $user->phone;
            $jsonObject['authenticated'] = $user->authenticated;
            $jsonObject['password'] = base64_decode($user->ch_password);
            $jsonObject['referal_code'] = $user->referal_code;
            $jsonObject['referal_from'] = $user->referal_from;
            $referal_from_name = Tbl_customer::where('id', $user->referal_from)->first();
            $jsonObject['referal_from_name'] = (isset($referal_from_name)) ? $referal_from_name->first_name : '';
            $jsonObject['wallet_balance'] = $user->wallet_balance;
            $jsonObject['image'] = asset('public/images/customer/'.$user->logo);
            $jsonObject['api_token'] = $user->api_token;

            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public function orders()
    {
        return $this->hasMany(Tbl_order::class, 'customer_id');
    }
}
