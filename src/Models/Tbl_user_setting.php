<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_user_setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'mail_from_address',
        'mail_from_name',
        'mail_host',
        'mail_port',
        'mail_user_name',
        'mail_password',
        'mail_encription',
        'header_custom_css_js',
        'footer_custom_css_js',
        'facebook_status',
        'facebook_app_id',
        'facebook_app_secret',
        'google_status',
        'google_client_id',
        'google_client_secret',
        'free_shipping_status',
        'free_shipping_label',
        'free_shipping_min_amount',
        'shipping_price',
        'local_shipping_status',
        'local_shipping_label',
        'local_shipping_cost',
        'paypal_status',
        'paypal_label',
        'paypal_desc',
        'paypal_api_user_name',
        'paypal_api_password',
        'paypal_api_signature',
        'cashondelivery_status',
        'cashondelivery_label',
        'cashondelivery_desc',
        'facebook_link',
        'twitter_link',
        'google_link',
        'linked_in_link',
        'invoice_terms_conditions',
        'referal_sender',
        'referal_receiver',
    ];
}
