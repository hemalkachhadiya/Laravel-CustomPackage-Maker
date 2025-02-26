<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_address_store extends Model{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'area',
        'address',
        'country',
        'state',
        'city',
        'pin',
        'status',
        'phone',
        'lat',
        'lang'
    ];

    public static function get_detail( $id ){
        $data = Tbl_address_store::where('id',$id)->where('status',1)->first();
        $jsonObject['id'] = $data->id;
        $jsonObject['area'] = ($data->area != NULL)?$data->area:"";
        $jsonObject['address'] = $data->address;
        $jsonObject['country'] = $data->country;
        $jsonObject['state'] = $data->state;
        $jsonObject['city'] = $data->city;
        $jsonObject['pin'] = $data->pin;
        $jsonObject['phone'] = $data->phone;
        $jsonObject['lat'] = $data->lat;
        $jsonObject['lang'] = $data->lang;
        return $jsonObject;
    }

}
