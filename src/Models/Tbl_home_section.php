<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_home_section extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $append = [
        'productdetail',
    ];

    protected $fillable = [
        'user_id',
        'status',
        'name',
        'product',
        'order_by',
        'radom',
    ];

    public function getProductdetailAttribute()
    {
        dd($this->product);
    }
}
