<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'order_id', 'notification', 'title', 'notification_type',
    ];

    public function notifications()
    {
        return $this->belongsTo(Tbl_order::class, 'order_id');
    }
}
