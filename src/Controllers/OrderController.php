<?php

namespace Smarttech\Prod\Controllers;

use Illuminate\Http\Request;
use Smarttech\Prod\Controllers\BaseController as BaseController;
use Smarttech\Prod\Controllers\CommonModal;
use Illuminate\Support\Facades\DB;
use Smarttech\Prod\Models\User;
use Smarttech\Prod\Models\Tbl_order;
use Smarttech\Prod\Models\Tbl_coupon;
use Smarttech\Prod\Models\Tbl_customer;
use Smarttech\Prod\Models\Tbl_product;
use Smarttech\Prod\Models\Tbl_order_bill;
use Smarttech\Prod\Models\Tbl_order_product;
use Smarttech\Prod\Models\Tbl_product_image;
use Smarttech\Prod\Models\Tbl_address_customer;
use Smarttech\Prod\Models\Tbl_order_status_update;
use Smarttech\Prod\Models\Tbl_img_prescription;
use Illuminate\Support\Facades\Validator;
// use App\Events\SendNotification;
use Smarttech\Prod\Models\Tbl_web_notification;
use Smarttech\Prod\Models\Tbl_address_store;
use Smarttech\Prod\Models\Tbl_wallets_transaction;
use Smarttech\Prod\Models\Tbl_order_payment_history;
use Smarttech\Prod\Models\CommonModel;
use Illuminate\Support\Facades\File;

class OrderController extends BaseController {
    private $apiToken;

    public function __construct() {

    }

    public function place_order( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( $user ) {
            // $rules = [
            //     'order'=>'required',
            // ];
            // $validator = Validator::make( $request->all(), $rules );
            // if ( $validator->fails() ) {
            //     $errorString = implode( ',', $validator->messages()->all() );
            //     return $this->sendError( $errorString, '' );
            // } else {
                $today = date( 'Ymd' );
                $rand = strtoupper( substr( uniqid( sha1( time() ) ), 0, 4 ) );
                $order_number = 'ORD'.$today.$user->id.$rand;

                $order = json_decode( $request->order );
                // dd($order);

                $address = $order->address[0];
                // dd($address);
                $items = $order->order_item;
                // dd($address->phone);

                $add_order['customer_id'] = $user->id;
                $add_order['currency'] = $user->currency;
                $add_order['order_number'] = $order_number;
                $add_order['invoice_no'] = $order_number;
                $add_order['order_type'] = "0";
                $add_order['phone'] = $address->phone;
                $add_order['email'] = $address->email;
                $add_order['first_name'] = $address->first_name;
                $add_order['last_name'] = $address->last_name ?? 'N/A';
                $add_order['address'] = $address->address;
                $add_order['city'] = $address->city;
                $add_order['pin_code'] = $address->pin;
                $add_order['country'] = $address->country;
                $add_order['state'] = $address->state;
                if ( $order->order_date != '' ) {
                    $add_order['order_date'] = $order->order_date;
                }
                // $add_order['payment_type'] = $order->payment_type;
                $add_order['grand_total'] = $order->grand_total;
                if ( isset( $order->description ) ) {
                    $add_order['note'] = $order->description;
                }
                $add_order['note'] = $order->description;
                if ( $order->coupon_code == '' ) {
                    $add_order['coupon'] = Null;
                } else {
                    $add_order['coupon'] = $order->coupon_code;
                }
                // if ( $order->discount_type == '' ) {
                //     $add_order['discount_type'] = null;
                // } else {
                //     $add_order['discount_type'] = $order->discount_type;
                // }
                // if ( $order->discount_value == '' ) {
                //     $add_order['discount_value'] = null;
                // } else {
                //     $add_order['discount_value'] = $order->discount_value;
                // }
                // if ( $order->discount == 0 ) {
                //   $add_order['discount'] = null;
                // } else {
                // $add_order['discount'] = $order->discount;
                // }
                $add_order['sub_total'] = $order->sub_total;
                $add_order['shipping_status'] = 'Pendding';
                $add_order['order_status'] = 'New';
                $add_order['payment_status'] = 'Unpaid';
                $add_order['total_recived'] = '0';

                $data = Tbl_order::create( $add_order );
                $order_id = $data->id;

                $statusdata['order_id'] = $order_id;
                $statusdata['status'] = 'Pendding';
                Tbl_order_status_update::create( $statusdata );

                $add_order_bill['order_id'] = $order_id;
                $add_order_bill['first_name'] = $data->first_name ?? 'N/A';
                $add_order_bill['last_name'] = $data->last_name ?? 'N/A';
                //$add_order_bill['company_name'] = $order->first_name;
                $add_order_bill['address'] = $data->address;
                $add_order_bill['country'] = $data->country;
                $add_order_bill['state'] = $data->state;
                $add_order_bill['city'] = $data->city;
                $add_order_bill['pin'] = $data->pin_code;
                $add_order_bill['phone'] = $data->phone;
                Tbl_order_bill::create( $add_order_bill );
                // dd($order);
                // dd($items);
                foreach ( $items as $row ) {

                    $product = Tbl_product::where( 'id', $row->id )->first();
                    $proimage = Tbl_product_image::where( 'product_id', '=', $row->id )->where( 'type', '=', '0' )->first();
                    // dd($product);
                    if(!is_null($product->pro_price) && !is_null($row->pro_qty)){
                    $total = $row->pro_qty * $product->pro_price;
                    }else{
                    $total = "0";
                    }
                    $add_order_pro['order_id'] = $order_id;
                    $add_order_pro['user_id'] = $product->user_id;
                    $add_order_pro['pro_id'] = $row->id;
                    $add_order_pro['pro_name'] = $product->name ?? 'N?A';
                    $add_order_pro['pro_sku'] = $row->pro_sku ?? 0;
                    $add_order_pro['pro_description'] = $product->short_description;
                    $add_order_pro['pro_price'] = $product->pro_price;
                    $add_order_pro['pro_qty'] = $row->pro_qty;
                    $add_order_pro['total_amount'] = $total;
                    $add_order_pro['option'] = json_encode( $row->add_pro_option );
                    $add_order_pro['prescription_required'] = $product->prescription_required;
                    if ( $product->prescription_required == 1 ) {
                        $add_order_pro['confirm'] = 0;
                    } else {
                        $add_order_pro['confirm'] = 1;
                    }
                    if ( !empty( $proimage ) ) {
                        $add_order_pro['image_id'] = $proimage->image_id;
                    }
                    //$add_order_pro['shipping_price'] = $key['quantity']*$product_details['shipping_charge'];
                    Tbl_order_product::create( $add_order_pro );
                }
                if ( isset( $request->prescription_img ) ) {
                    foreach ( $request->prescription_img as $image ) {
                        $image = $image;
                        $name = time().uniqid().'.'.$image->getClientOriginalExtension();
                        $destinationPath = public_path( '/images/prescription_images' );
                        $image->move( $destinationPath, $name );

                        $img['image'] = $name;
                        $img['customer_id'] = $user->id;
                        $img['order_id'] =  $order_id;
                        $img['type'] = 1;
                        Tbl_img_prescription::create( $img );
                    }

                }
                if ( isset( $request->exist ) ) {
                    // return $request->exist;
                    $exist = json_decode( $request->exist );
                    $image = Tbl_img_prescription::whereIn( 'id', $exist )->get();
                    foreach ( $image as $row ) {
                        $imgname =  explode( '.', $row->image );
                        $img_name = time().uniqid().'.'.$imgname[1];

                        $old_img = public_path( '/images/prescription_images/'.$row->image );

                        $new_img = public_path( '/images/prescription_images/'.$img_name );
                        File::copy( $old_img, $new_img );

                        $img['image'] = $img_name;
                        $img['customer_id'] = $user->id;
                        $img['order_id'] =  $order_id;
                        $img['type'] = 1;

                        Tbl_img_prescription::create( $img );
                    }
                }

                if ( isset( $data ) ) {
                    $create['order_id'] = $order_id;
                    $create['message'] = 'New Order ('.$data->order_number.')';
                    $create['type'] = 0;
                    $notification = Tbl_web_notification::create( $create );
                    // event( new SendNotification( ''.$notification->id.'', ''.$create['message'].'', ''.$create['type'].'', ''.route( 'orders.edit', [app()->getLocale(), $data->id] ).'' ) );
                    return $this->sendResponse( $data, 'Order Placed successfully' );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            // }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function place_order_with_prescription( Request $request ) {

        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( $user ) {
            $rules = [
                'self_pickup' => 'required',
                'address' => 'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $today = date( 'Ymd' );
                $rand = strtoupper( substr( uniqid( sha1( time() ) ), 0, 4 ) );
                $order_number = 'ORD'.$today.$user->id.$rand;

                $order = json_decode( $request->address );
                $address = $order->address;

                $add_order['customer_id'] = $user->id;
                $add_order['currency'] = $user->currency;
                $add_order['order_number'] = $order_number;
                $add_order['invoice_no'] = $order_number;
                $add_order['order_type'] = 0;
                if ( $request->self_pickup == 1 ) {
                    $add_order['self_pickup'] = $request->self_pickup;
                    $add_order['phone'] = $user->phone;
                    $add_order['email'] = $user->email;
                    $add_order['first_name'] = $user->first_name;
                    $add_order['last_name'] = $user->last_name;
                    if ( $address->address == '' ) {
                        $add_order['address'] = 'shop no. 1 shankheshwar complex, opp., Parvat Patiya Flyover';
                        $add_order['city'] = 'Surat';
                        $add_order['pin_code'] = '395002';
                        $add_order['country'] = 'india';
                        $add_order['state'] = 'Gujarat';
                    } else {
                        $store_address = Tbl_address_store::where( 'id', $address->address )->first();
                        $add_order['address'] = $store_address->address;
                        $add_order['city'] = $store_address->city;
                        $add_order['pin_code'] = $store_address->pin;
                        $add_order['country'] = $store_address->country;
                        $add_order['state'] = $store_address->state;
                    }
                } else {
                    $add_order['phone'] = $address->phone;
                    $add_order['email'] = $address->email;
                    $add_order['first_name'] = $address->first_name;
                    $add_order['last_name'] = $address->last_name;
                    $add_order['address'] = $address->address;
                    $add_order['city'] = $address->city;
                    $add_order['pin_code'] = $address->pin;
                    $add_order['country'] = $address->country;
                    $add_order['state'] = $address->state;

                }
                $add_order['lat'] = ( isset( $address->lat ) && $address->lat != '' )?$address->lat:0;
                $add_order['lng'] = ( isset( $address->lang ) && $address->lang != '' )?$address->lang:0;

                if ( $request->order_date != '' ) {
                    $add_order['order_date'] = date( 'Y-m-d', strtotime( $request->order_date ) );
                }
                // $add_order['payment_type'] = $order->payment_type;
                if ( isset( $order->description ) ) {
                    $add_order['note'] = $order->description;
                }
                $add_order['shipping_status'] = 'Pendding';
                $add_order['order_status'] = 'New';
                $add_order['payment_status'] = 'Unpaid';
                $add_order['total_recived'] = '0';
                if ( isset( $request->device_type ) ) {
                    $add_order['device_type'] = $request->device_type;
                }
                if ( isset( $request->app_version ) ) {
                    $add_order['app_version'] = $request->app_version;
                }
                $data = Tbl_order::create( $add_order );

                $statusdata['order_id'] = $data->id;
                $statusdata['status'] = 'Pendding';
                Tbl_order_status_update::create( $statusdata );

                $add_order_bill['order_id'] = $data->id;
                $add_order_bill['first_name'] = $data->first_name;
                $add_order_bill['last_name'] = $data->last_name;
                $add_order_bill['address'] = $data->address;
                $add_order_bill['country'] = $data->country;
                $add_order_bill['state'] = $data->state;
                $add_order_bill['city'] = $data->city;
                $add_order_bill['pin'] = $data->pin_code;
                $add_order_bill['phone'] = $data->phone;
                $add_order_bill['alternativ_phone'] = ( isset( $address->alternativ_phone ) )?$address->alternativ_phone:NULL;
                $add_order_bill['landmark'] = ( isset( $address->landmark ) )?$address->landmark:'';
                $add_order_bill['lat'] = ( isset( $address->lat ) && $address->lat != '' )?$address->lat:0;
                $add_order_bill['lang'] = ( isset( $address->lang ) && $address->lang != '' )?$address->lang:0;
                Tbl_order_bill::create( $add_order_bill );

                if ( isset( $request->image ) ) {
                    foreach ( $request->image as $image ) {
                        //$image = $request->prescription_img;
                        $name = time().uniqid().'.'.$image->getClientOriginalExtension();
                        $destinationPath = public_path( '/images/prescription_images' );
                        $image->move( $destinationPath, $name );

                        $img['image'] = $name;
                        $img['customer_id'] = $user->id;
                        $img['order_id'] =  $data->id;
                        $img['type'] = 1;
                        Tbl_img_prescription::create( $img );
                    }
                }
                if ( isset( $request->exist ) ) {
                    $exist = json_decode( $request->exist );
                    $image = Tbl_img_prescription::whereIn( 'id', $exist )->get();
                    foreach ( $image as $row ) {
                        $imgname =  explode( '.', $row->image );
                        $img_name = time().uniqid().'.'.$imgname[1];

                        $old_img = public_path( '/images/prescription_images/'.$row->image );

                        $new_img = public_path( '/images/prescription_images/'.$img_name );
                        File::copy( $old_img, $new_img );

                        $img['image'] = $img_name;
                        $img['customer_id'] = $user->id;
                        $img['order_id'] =  $data->id;
                        $img['type'] = 1;

                        Tbl_img_prescription::create( $img );
                    }
                }

                if ( isset( $data ) ) {
                    $create['order_id'] = $data->id;
                    $create['message'] = 'New Order ('.$data->order_number.')';
                    $create['type'] = 0;
                    $notification = Tbl_web_notification::create( $create );
                    // event( new SendNotification( ''.$notification->id.'', ''.$create['message'].'', ''.$create['type'].'', ''.route( 'orders.edit', [app()->getLocale(), $data->id] ).'' ) );
                    return $this->sendResponse( $data, 'Order Placed successfully' );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function re_order( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( !empty( $user ) ) {
            $rules = [
                'order_id' => 'required',
                'order_date' => 'required'
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $order = Tbl_order::where( 'id', $request->order_id )
                ->where( 'customer_id', $user->id )
                ->first();
                $today = date( 'Ymd' );
                $rand = strtoupper( substr( uniqid( sha1( time() ) ), 0, 4 ) );
                $order_number = 'ORD'.$today.$user->id.$rand;

                $order_data['order_number'] = $order_number;
                $order_data['invoice_no'] = $order_number;
                $order_data['customer_id'] = $order->customer_id;
                $order_data['phone'] = $order->phone;
                $order_data['email'] = $order->email;
                $order_data['first_name'] = $order->first_name;
                $order_data['last_name'] = $order->last_name;
                $order_data['address'] = $order->address;
                $order_data['city'] = $order->city;
                $order_data['pin_code'] = $order->pin_code;
                $order_data['country'] = $order->country;
                $order_data['state'] = $order->state;
                $order_data['order_date'] = $order->order_date;
                // $order_data['payment_type'] = $order->payment_type;
                $order_data['shipping_status'] = 'Pendding';
                $order_data['order_status'] = 'New';
                $order_data['discount'] = '0';
                $order_data['coupon'] = NULL;
                $order_data['discount_type'] = NULL;
                $order_data['discount_value'] = '0';
                $order_data['payment_status'] = 'Unpaid';
                $order_data['total_recived'] = '0';
                $order_data['device_type'] = $order->device_type;
                $order_data['app_version'] = $order->app_version;
                $data = Tbl_order::create( $order_data );

                $statusdata['order_id'] = $data->id;
                $statusdata['status'] = 'Pendding';
                Tbl_order_status_update::create( $statusdata );

                $add_order_bill['order_id'] = $data->id;
                $add_order_bill['first_name'] = $data->first_name;
                $add_order_bill['last_name'] = $data->last_name;
                //$add_order_bill['company_name'] = $order->first_name;
                $add_order_bill['address'] = $data->address;
                $add_order_bill['country'] = $data->country;
                $add_order_bill['state'] = $data->state;
                $add_order_bill['city'] = $data->city;
                $add_order_bill['pin'] = $data->pin_code;
                $add_order_bill['phone'] = $data->phone;
                Tbl_order_bill::create( $add_order_bill );

                $items = Tbl_order_product::where( 'order_id', $request->order_id )->get();
                foreach ( $items as $row ) {
                    $product = Tbl_product::where( 'id', '=', $row->pro_id )->first();

                    if ( !empty( $product ) ) {

                        $proimage = Tbl_product_image::where( 'product_id', '=', $row->pre_id )->where( 'type', '=', '0' )->first();

                        $add_order_pro['order_id'] = $data->id;
                        $add_order_pro['user_id'] = $product->user_id;
                        $add_order_pro['pro_id'] = $product->id;
                        $add_order_pro['pro_name'] = $product->name;
                        $add_order_pro['pro_sku'] = $product->sku;
                        $add_order_pro['pro_description'] = $product->short_description;
                        if ( $product->discount != 0 ) {
                            $add_order_pro['pro_price'] = $product->price*$product->discount/100;
                        } else {
                            $add_order_pro['pro_price'] = $product->price;
                        }
                        $add_order_pro['pro_qty'] = $row->pro_qty;
                        $add_order_pro['total_amount'] = $row->total_amount;
                        $add_order_pro['option'] = json_encode( $row->add_pro_option );
                        $add_order_pro['prescription_required'] = $product->prescription_required;
                        if ( !empty( $proimage ) ) {
                            $add_order_pro['image_id'] = $proimage->image_id;
                        }
                        //$add_order_pro['shipping_price'] = $key['quantity']*$product_details['shipping_charge'];
                        Tbl_order_product::create( $add_order_pro );
                    }
                }

                $order_product = Tbl_order_product::where( 'order_id', $data->id )->get();
                $total = 0;
                foreach ( $order_product as $row ) {
                    $total = $total + $row->total_amount;

                }
                $up_data['sub_total'] = $total;
                $up_data['grand_total'] = $total;

                Tbl_order::where( 'id', $data->id )->update( $up_data );

                if ( !empty( $data ) ) {
                    return $this->sendResponse( $data, 'Order Placed successfully' );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function check_coupon( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( $user ) {
            $rules = [
                'code'=>'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $check_coupon  = Tbl_coupon::where( 'code', '=', $request->code )->first();
                if ( isset( $check_coupon ) ) {
                    if ( $check_coupon->status == 1 ) {
                        $current_date = date( 'Y-m-d' );
                        if ( $current_date >  $check_coupon->start_date OR $current_date ==  $check_coupon->start_date ) {
                            if ( $current_date < $check_coupon->end_date ) {
                                $count = Tbl_order::where( 'customer_id', '=', $user->id )
                                ->where( 'coupon', '=', $check_coupon->code )
                                ->get()
                                ->count();
                                if ( $count < $check_coupon->limit_use_per_person ) {
                                    $coupon = CommonModal::coupon_details( $check_coupon->id );
                                    if ( sizeof( $coupon ) > 0 ) {
                                        return $this->sendResponse( $coupon, 'Details Load successfully' );
                                    } else {
                                        return $this->sendError( 'Coupon not found', '' );
                                    }
                                } else {
                                    return $this->sendError( 'Coupon apply limit Over.', '' );
                                }
                            } else {
                                return $this->sendError( 'Coupon has been expired.', '' );
                            }
                        } else {
                            return $this->sendError( 'Coupon not found', '' );
                        }
                    } else {
                        return $this->sendError( 'Coupon not activated.', '' );
                    }
                } else {
                    return $this->sendError( 'Coupon not found.', '' );
                }
                $current_date = date( 'Y-m-d' );
                if ( $current_date < $check_coupon->end_date ) {
                    $count = Tbl_order::where( 'customer_id', '=', $user->id )
                    ->where( 'coupon', '=', $check_coupon->code )
                    ->get()
                    ->count();
                    if ( $count < $check_coupon->limit_use_per_person ) {
                        $coupon = CommonModal::coupon_details( $check_coupon->id );
                        if ( sizeof( $coupon ) > 0 ) {
                            return $this->sendResponse( $coupon, 'Details Load successfully' );
                        } else {
                            return $this->sendError( 'List not found', '' );
                        }
                    } else {
                        return $this->sendError( 'Coupon apply limit Over.', '' );
                    }
                } else {
                    return $this->sendError( 'Coupon has been expired.', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function pending_order( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( !empty( $user ) ) {
            $rules = [
                'limit'=>'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $data = CommonModal::pending_order( $user->id, $request->limit );
                if ( sizeof( $data )>0 ) {
                    $offset = $request->limit+10;
                    return $this->sendResponsePagination( $data, 'List load successfully.', $offset );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function complete_order( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( !empty( $user ) ) {
            $rules = [
                'limit'=>'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $data = CommonModal::complete_order( $user->id, $request->limit );
                if ( sizeof( $data )>0 ) {
                    $offset = $request->limit+10;
                    return $this->sendResponsePagination( $data, 'List load successfully.', $offset );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function order_details( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        // dd($user);
        if ( !empty( $user ) ) {
            $rules = [
                'order_id'=>'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $data = CommonModal::order_details( $user->id, $request->order_id );
                if ( sizeof( $data )>0 ) {
                    return $this->sendResponse( $data, 'List load successfully.' );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function cancle_order( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( !empty( $user ) ) {
            $rules = [
                'order_id' => 'required',
                'reason' => 'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $order = Tbl_order::where( 'id', $request->order_id )
                ->where( 'customer_id', $user->id )
                ->where( 'order_status', '!=', 'Canceled' )
                ->where( 'cancle_order', NULL )
                ->first();
                if ( isset( $order ) ) {
                    $update_data['order_status'] = 'Canceled';
                    $update_data['cancle_order'] = $request->reason;
                    $data = Tbl_order::where( 'id', $request->order_id )->where( 'customer_id', $user->id )->update( $update_data );

                    $statusdata['order_id'] = $request->order_id;
                    $statusdata['status'] = 'Canceled';
                    Tbl_order_status_update::create( $statusdata );

                    Tbl_web_notification::where( 'order_id', $request->order_id )->update( ['is_read'=>1] );

                    $add_wallet = 0;
                    $payment = Tbl_order_payment_history::where('order_id',$request->order_id)->where('payment_method',"-")->get();
                    foreach ($payment as $row) {
                        $add_wallet = $add_wallet + $row->amount;
                    }
                    if ($add_wallet != 0) {
                        $update['user_id'] = $user->id;
                        $update['message'] = "Order cancellation credit";
                        $update['amount'] = $add_wallet;
                        $update['transaction_type'] = 0;
                        $update['transaction_method'] = 1;
                        $update['transaction_obj'] = $order->id;
                        Tbl_wallets_transaction::create($update);

                        Tbl_order_payment_history::create(
                            [
                                'order_id'          => $order->id,
                                'amount'            => $add_wallet,
                                'type'              => 0,
                                'payment_method'    => '+'
                            ]
                        );

                        Tbl_order::where( 'id', $order->id )->update(array(
                                'wallet' => 0,
                                'total_recived' => 0,
                                'payment_status' => 'Unpaid',
                                'payment_type' => ''
                            )
                        );

                        CommonModel::calculate_wallet_balance($user->id);
                    }
                    return $this->sendResponse( $data, 'Order Cancle successfully' );
                } else {
                    return $this->sendError( 'Order not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }

    public function order_tracking( Request $request ) {
        $token = $request->api_token;
        $user = Tbl_customer::where( 'api_token', $token )->first();
        if ( !empty( $user ) ) {
            $rules = [
                'order_id' => 'required',
            ];
            $validator = Validator::make( $request->all(), $rules );
            if ( $validator->fails() ) {
                $errorString = implode( ',', $validator->messages()->all() );
                return $this->sendError( $errorString, '' );
            } else {
                $data = Tbl_order_status_update::where( 'order_id', $request->order_id )
                ->orderby( 'created_at', 'ASC' )
                ->get();
                if ( !empty( $data ) ) {
                    return $this->sendResponse( $data, 'List load successfully.' );
                } else {
                    return $this->sendError( 'List not found', '' );
                }
            }
        } else {
            return $this->sendError( 'User not found', '' );
        }
    }
}
