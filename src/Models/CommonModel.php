<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class CommonModel extends Model
{
    public static function number_format($number)
    {
        return number_format($number, 2, '.', '');
    }

    public static function calculate_order_final_price($order_id)
    {
        $user_setting = Tbl_user_setting::where('user_id', 8)->first();

        $order_details = Tbl_order::where('id', $order_id)->first();
        $newsub_total = 0;
        $update_order['price_discount'] = 0;
        $updated_data = Tbl_order_product::where('order_id', '=', $order_id)->get();
        foreach ($updated_data as $key) {
            $newsub_total += CommonModel::number_format($key['pro_mrp_price'] * $key['pro_qty']);
            if ($key['pro_discount_price'] != null) {
                $update_order['price_discount'] += $key['pro_discount_price'];
            }
        }
        $coupon_details = Tbl_coupon::where('code', $order_details->coupon)->first();
        $shipping_price = $order_details['shipping_price'];
        $update_order['sub_total'] = CommonModel::number_format($newsub_total);
        if (isset($coupon_details->minimum_cart_amount)) {
            if ($newsub_total >= $coupon_details->minimum_cart_amount) {
                if ($order_details->discount_type == 1) {
                    $des = ($update_order['sub_total'] - $update_order['price_discount']) * $order_details->discount_value / 100;
                    $update_order['discount'] = CommonModel::number_format($des);
                } else {
                    $des = ($update_order['sub_total'] - $update_order['price_discount']) - $order_details->discount_value;
                    $update_order['discount'] = CommonModel::number_format($des);
                }
            } else {
                $update_order['discount'] = 0;
            }
            if ($update_order['discount'] > $coupon_details->maximun_spend) {
                $update_order['discount'] = $coupon_details->maximun_spend;
            }
        } else {
            $update_order['discount'] = 0;
        }
        $update_order['grand_total'] = round((float) $newsub_total - $update_order['price_discount'] - $update_order['discount']);
        if ($order_details['self_pickup'] != 1) {
            if ($user_setting->shipping_price < $order_details->shipping_price) {
                $update_order['shipping_price'] = $order_details->shipping_price;
                $update_order['grand_total'] = $update_order['grand_total'] + $order_details->shipping_price;
            } else {
                if ($update_order['grand_total'] < $user_setting->free_shipping_min_amount or $update_order['grand_total'] == $user_setting->free_shipping_min_amount) {
                    $update_order['shipping_price'] = $user_setting->shipping_price;
                    $update_order['grand_total'] = $update_order['grand_total'] + $user_setting->shipping_price;
                } else {
                    $update_order['shipping_price'] = 0;
                }
            }
        } else {
            $update_order['shipping_price'] = 0;
        }
        Tbl_order::where('id', $order_id)->update($update_order);
    }

    public static function generateReferalCode($length_of_string)
    {
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($str_result), 0, $length_of_string);
    }

    public static function calculate_wallet_balance($user_id)
    {
        $transection = Tbl_wallets_transaction::where('user_id', $user_id)->orderby('id', 'ASC')->get();
        $data['wallet_balance'] = 0;
        foreach ($transection as $row) {
            if ($row->transaction_method == 0) {// minus -
                $data['wallet_balance'] = $data['wallet_balance'] - $row->amount;
            } else {// plus +
                $data['wallet_balance'] = $data['wallet_balance'] + $row->amount;
            }
        }
        Tbl_customer::where('id', $user_id)->update($data);
    }
}
