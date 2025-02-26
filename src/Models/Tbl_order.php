<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_number',
        'invoice_no',
        'customer_id',
        'self_pickup',
        'phone',
        'email',
        'first_name',
        'last_name',
        'company_name',
        'address',
        'city',
        'pin_code',
        'country',
        'state',
        'order_date',
        'payment_type',
        'paypal_paymentToken',
        'paypal_orderID',
        'paypal_payerID',
        'paypal_paymentID',
        'razorpay_paymentID',
        'upi_paymentID',
        'currency',
        'currency_rate',
        'shipping_status',
        'order_status',
        'payment_status',
        'sub_total',
        'shipping_price',
        'discount',
        'coupon',
        'discount_type',
        'discount_value',
        'price_discount',
        'grand_total',
        'total_recived',
        'note',
        'forward',
        'lat',
        'lng',
        'cancle_order',
        'order_type',
        'description',
        'reject_reason',
        'doctor_name',
        'doctor_address',
        'paytm_mid',
        'paytm_status',
        'paytm_respcode',
        'paytm_respmsg',
        'paytm_banktxnid',
        'paytm_checksumhash',
        'paytm_gatewayname',
        'paytm_paymentmode',
        'paytm_txnid',
        'wallet',
        'device_type',
        'app_version',
    ];

    public static function order_count_by_status($url)
    {
        if ($url == 'orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'New')->count();
        } elseif ($url == 'self-pickup-orders') {
            return Tbl_order::where('order_type', 0)->where('self_pickup', 1)->where('order_status', 'Confirm')->count();
        } elseif ($url == 'delivery-orders') {
            return Tbl_order::where('order_type', 0)->where('self_pickup', null)->where('order_status', 'Confirm')->count();
        } elseif ($url == 'canceled-orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'Canceled')->count();
        } elseif ($url == 'rejected-orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'Reject')->count();
        } elseif ($url == 'delivered-orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'Delivered')->count();
        } elseif ($url == 'dispatch-orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'Dispatch')->count();
        } elseif ($url == 'forward-orders') {
            return Tbl_order::where('order_type', 0)->where('order_status', 'Forward')->count();
        } elseif ($url == 'req_medicines') {
            return Tbl_order::where('order_type', 1)->count();
        }

    }

    /**
     * Get the user that owns the Tbl_order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Tbl_customer::class, 'customer_id');
    }

    public function productplace()
    {
        return $this->hasMany(Tbl_order_product::class, 'order_id');
    }
}
