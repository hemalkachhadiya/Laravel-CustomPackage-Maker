<?php

namespace Smarttech\Prod\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_wallets_transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'message',
        'amount',
        'transaction_type', // 0=order,1 = own refrel, 2= refral from,
        'transaction_method', // 0=debit (-), 1=credit (+)
        'transaction_obj', // transaction object exc. (order_number)
    ];

    public static function get_details($id)
    {
        $object = [];
        $data = Tbl_wallets_transaction::where('id', $id)->first();
        if (isset($data)) {
            $object['id'] = $data->id;
            $object['user_id'] = $data->user_id;
            $object['message'] = $data->message;
            $object['display_name'] = '';
            if ($data->transaction_type == 0) {
                $display_name = Tbl_order::where('id', $data->transaction_obj)->first();
                if (isset($display_name)) {
                    $object['display_name'] = $display_name->order_number;
                }
            } elseif ($data->transaction_type == 1 || $data->transaction_type == 2) {
                $display_name = Tbl_customer::where('id', $data->transaction_obj)->first();
                if (isset($display_name)) {
                    $object['display_name'] = $display_name->first_name;
                }
            }
            $object['amount'] = $data->amount;
            $object['transaction_type'] = $data->transaction_type;
            $object['transaction_method'] = $data->transaction_method;
            $object['transaction_obj'] = $data->transaction_obj;
            $object['created_at'] = date('d-m-Y', strtotime($data->created_at));
        }

        return $object;
    }
}
