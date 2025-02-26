<?php

namespace Smarttech\Prod\Controllers;

use Illuminate\Support\Facades\DB;
use Smarttech\Prod\Models\Tbl_address_customer;
use Smarttech\Prod\Models\Tbl_categorie;
// use Smarttech\Prod\Models\Tbl_categorie;
use Smarttech\Prod\Models\Tbl_coupon;
use Smarttech\Prod\Models\Tbl_customer;
use Smarttech\Prod\Models\Tbl_home_section;
use Smarttech\Prod\Models\Tbl_image;
use Smarttech\Prod\Models\Tbl_img_prescription;
use Smarttech\Prod\Models\Tbl_manufature;
use Smarttech\Prod\Models\Tbl_notification;
use Smarttech\Prod\Models\Tbl_order;
use Smarttech\Prod\Models\Tbl_order_product;
use Smarttech\Prod\Models\Tbl_product;
use Smarttech\Prod\Models\Tbl_product_image;
use Smarttech\Prod\Models\Tbl_product_salt;
use Smarttech\Prod\Models\Tbl_salt;
use Smarttech\Prod\Models\Tbl_slider;
use Smarttech\Prod\Models\User;

class CommonModal
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sliderlist()
    {
        $jsonArray = [];

        $slider = Tbl_slider::where('type', '=', '0')->where('is_active', '=', '1')->get();
        $categorie = Tbl_categorie::orderby('id', 'DESC')->limit(5)->get();
        if (isset($slider)) {
            foreach ($slider as $s) {
                $jsonObject['id'] = $s->id;
                $jsonObject['name'] = $s->name;
                $jsonObject['link'] = $s->link;
                $jsonObject['image'] = asset('public/images/slider/'.$s->logo);
                if ($s->category == null) {
                    $jsonObject['category'] = '';
                } else {
                    $jsonObject['category'] = $s->category;
                }

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function categorie_list()
    {

        $jsonArray = [];
        $category = Tbl_categorie::where('type', 0)->where('status', 1)->get();
        if (isset($category)) {
            foreach ($category as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['name'] = $row->name;
                $jsonObject['slug'] = $row->slug;
                $jsonObject['type'] = $row->type;
                if ($row->image == null) {
                    $jsonObject['image'] = asset('public/images/product_categorie/category.png');
                } else {
                    $jsonObject['image'] = asset('public/images/product_categorie/'.$row->image);
                }
                $jsonObject['categories_id'] = $row->categories_id;
                // $jsonObject['user_id'] = $row->user_id;
                // $jsonObject['searchable'] = $row->searchable;
                // $jsonObject['status'] = $row->status;
                // $jsonObject['seo'] = $row->seo;
                // $jsonObject['created_at'] = $row->created_at;
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function pro_images($pro_id)
    {
        $jsonArray = [];
        $img = Tbl_product_image::where('product_id', $pro_id)->get();
        if (isset($img)) {
            foreach ($img as $row) {
                $r = Tbl_image::where('id', $row->image_id)->first();
                $jsonArray[] = asset('public/images/product/'.$r->image);
            }
        } else {
            $jsonArray[] = asset('public/images/product/product.png');
        }

        return $jsonArray;
    }

    public static function get_home_section($id)
    {
        $jsonArray = [];
        $section = Tbl_home_section::where('id', $id)->first();
        $product = new Tbl_product;
        $product = $product->whereIn('id', \json_decode($section->product))->where('status', 1);
        if ($section->radom == 1) {
            $product = $product->inRandomOrder();
        } else {
            $product = $product->orderByRaw('FIELD(`id` ,'.implode(',', \json_decode($section->product)).')');
        }
        $product = $product->limit(5)->get();

        if (count($product) > 0) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                } else {
                    $jsonObject['discount_price'] = $row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = [];
                    $jsonObject['image'][] = $row->pro_img;
                } else {
                    $jsonObject['image'] = self::pro_images($row->id);
                }

                if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);

                $jsonArray['name'] = $section->name;
                $jsonArray['items'][] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    /*
    public static function seasonal_product(){
        $jsonArray = array();
        $product = Tbl_product::limit(5)->get();
        if (isset($product)) {
            foreach($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price*$row->discount/100;
                }else{
                    $jsonObject['discount_price'] =$row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = array();
                    $jsonObject['image'][] = $row->pro_img;
                }else{
                    $jsonObject['image']= self::pro_images($row->id);
                }

                if(trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet'){
                    $form = 'Strip';
                }else{
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form)." of ".trim($row->package_per_unit);

                $jsonArray['name'] = "Seasonaol Product";
                $jsonArray['items'][] = $jsonObject;
            }
        }
        return $jsonArray;
    }

    public static function popular_product(){
        $jsonArray = array();
        $product = Tbl_product::limit(5)->get();
        if (isset($product)) {
            foreach($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price*$row->discount/100;
                }else{
                    $jsonObject['discount_price'] =$row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = array();
                    $jsonObject['image'][] = $row->pro_img;
                }else{
                    $jsonObject['image']= self::pro_images($row->id);
                }

                if(trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet'){
                    $form = 'Strip';
                }else{
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form)." of ".trim($row->package_per_unit);

                $jsonArray['name'] = "Popular Product";
                $jsonArray['items'][] = $jsonObject;
            }
        }
        return $jsonArray;
    }

    public static function top_selling_product(){
        $jsonArray = array();
        $product = Tbl_product::limit(5)->get();
        if (isset($product)) {
            foreach($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price*$row->discount/100;
                }else{
                    $jsonObject['discount_price'] =$row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = array();
                    $jsonObject['image'][] = $row->pro_img;
                }else{
                    $jsonObject['image']= self::pro_images($row->id);
                }
                if(trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet'){
                    $form = 'Strip';
                }else{
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form)." of ".trim($row->package_per_unit);
                $jsonArray['name'] = "Top Selling";
                $jsonArray['items'][] = $jsonObject;
            }
        }
        return $jsonArray;
    }
    */
    public static function categorie_detail($id)
    {

        $jsonArray = [];
        $category = Tbl_categorie::where('id', '=', $id)->first();
        if (isset($category)) {
            $jsonObject['id'] = $category->id;
            $jsonObject['user_id'] = $category->user_id;
            $jsonObject['name'] = $category->name;
            $jsonObject['slug'] = $category->slug;
            $jsonObject['searchable'] = $category->searchable;
            $jsonObject['status'] = $category->status;
            $jsonObject['seo'] = $category->seo;
            $jsonObject['type'] = $category->type;
            $jsonObject['image'] = asset('public/images/product_categorie/'.$category->image);
            $jsonObject['categories_id'] = $category->categories_id;
            $jsonObject['created_at'] = $category->created_at;
            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function product_list($limit)
    {
        $jsonArray = [];
        $product = Tbl_product::where('status', 1)
            ->offset($limit)
            ->limit(10)
            ->get();
        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                } else {
                    $jsonObject['discount_price'] = $row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['status'] = $row->status;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = [];
                    $jsonObject['image'][] = $row->pro_img;
                } else {
                    $jsonObject['image'] = self::pro_images($row->id);
                }

                if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function get_section_product_list($name, $limit)
    {
        $jsonArray = [];
        $section = Tbl_home_section::where('status', 1)
            ->where('name', $name)
            ->first();
        $product = Tbl_product::whereIn('id', json_decode($section->product))
            ->where('status', 1)
            ->orderByRaw('FIELD(`id` ,'.implode(',', \json_decode($section->product)).')')
                                // ->orderby('id','DESC')
            ->offset($limit)
            ->limit(10)
            ->get();
        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                } else {
                    $jsonObject['discount_price'] = $row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['status'] = $row->status;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = [];
                    $jsonObject['image'][] = $row->pro_img;
                } else {
                    $jsonObject['image'] = self::pro_images($row->id);
                }

                if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function product_list_by_categorie($limit, $category)
    {
        $jsonArray = [];

        // $product = Tbl_product::whereRaw('json_contains(categories, \'["' . $category . '"]\')')
        //                         ->offset($limit)
        //                         ->limit(10)
        //                         ->get();

        $categories = DB::select('SELECT * FROM tbl_categories WHERE id = "'.$category.'" OR id IN (SELECT id FROM tbl_categories WHERE categories_id = "'.$category.'")');
        $sql = [];

        // foreach($categories as $key => $value){
        //     $keydata = '"'.$categories[$key]->id.'"';
        //     $sql[] = "categories".","." LIKE".","." '%".$keydata."%'";
        // }
        // return json_encode($sql);

        foreach ($categories as $key => $value) {
            $keydata = '"'.$categories[$key]->id.'"';
            $sql[] = "categories LIKE '%".$keydata."%'";
        }
        // return $sql;
        $product = DB::select("SELECT * FROM tbl_products WHERE status = '1' AND (".implode(' OR ', $sql).') limit '.$limit.',10');

        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                } else {
                    $jsonObject['discount_price'] = $row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = [];
                    $jsonObject['image'][] = $row->pro_img;
                } else {
                    $jsonObject['image'] = self::pro_images($row->id);
                }

                if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function user_details($id)
    {
        $jsonArray = [];
        $user = Tbl_customer::where('id', '=', $id)->first();
        if (isset($user)) {

            $jsonObject['id'] = $user->id;
            // $jsonObject['user_id'] = $user->user_id;
            $jsonObject['first_name'] = $user->first_name;
            $jsonObject['last_name'] = $user->last_name;
            $jsonObject['email'] = $user->email;
            $jsonObject['phone'] = $user->phone;
            $jsonObject['authenticated'] = $user->authenticated;
            $jsonObject['password'] = base64_decode($user->ch_password);
            // $jsonObject['gender'] = $user->gender;
            // $jsonObject['birth_date'] = $user->birth_date;
            // $jsonObject['currency'] = $user->currency;
            // $jsonObject['address'] = $user->address;
            $jsonObject['image'] = asset('public/images/customer/'.$user->logo);

            $jsonObject['api_token'] = $user->api_token;

            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function salts($id)
    {
        $salt = '';
        $data = Tbl_product_salt::where('product_id', $id)->get();
        foreach ($data as $row) {
            $s = Tbl_salt::where('id', $row->salt_id)->first();
            $salt = $s->name.'('.$row->value.' '.$row->unit.')/'.$salt;
        }

        return $salt;
    }

    public static function salts_arr($id)
    {
        $array = [];
        $data = Tbl_product_salt::where('product_id', $id)->get();
        foreach ($data as $row) {
            $s = Tbl_salt::where('id', $row->salt_id)->first();
            $Object['salt_id'] = $row->salt_id;
            $Object['name'] = $s->name.'('.$row->value.' '.$row->unit.')';

            $array[] = $Object;
        }

        return $array;
    }

    public static function product_details($id)
    {
        $jsonArray = [];

        $product = Tbl_product::where('status', 1)->where('id', '=', $id)->first();
        if (isset($product)) {

            $jsonArray['id'] = $product->id;
            // $jsonObject['user_id'] = $product->user_id;
            $jsonArray['name'] = $product->name;
            // $jsonArray['composition'] = self::salts($product->id);
            // $jsonArray['composition_array'] = self::salts_arr($product->id);
            $jsonArray['generic_name'] = $product->generic_name;
            $jsonArray['manufature'] = Tbl_manufature::where('id', $product->manufature)->first()->name;
            $jsonArray['price'] = $product->price;
            $jsonArray['discount'] = $product->discount;
            if ($product->discount != 0) {
                $jsonArray['discount_price'] = $product->price - $product->price * $product->discount / 100;
            } else {
                $jsonArray['discount_price'] = $product->price;
            }
            $jsonArray['batch_no'] = $product->batch_no;
            $jsonArray['expiration_date'] = $product->expiration_date;
            $jsonArray['video_url'] = $product->video_url;
            $jsonArray['prescription_required'] = $product->prescription_required;
            $jsonArray['categories'] = $product->categories;
            $jsonArray['status'] = $product->status;
            $jsonArray['qty'] = $product->qty;
            if ($product->pro_img != '') {
                $jsonArray['image'] = [];
                $jsonArray['image'][] = $product->pro_img;
            } else {
                $jsonArray['image'] = self::pro_images($product->id);
            }

            if (trim($product->product_form) == 'Capsule' || trim($product->product_form) == 'Tablet') {
                $form = 'Strip';
            } else {
                $form = trim($product->product_form);
            }
            $jsonArray['package_per_unit'] = trim($form).' of '.trim($product->package_per_unit);
            if ($product->description == null) {
                $jsonArray['description'] = '';
            } else {
                $jsonArray['description'] = $product->description;
            }
            $jsonArray['stock_manage'] = $product->stock_manage;
        }
        $jsonArray1 = [];
        $jsonArray1['items'] = [];
        if (isset($product->related_product)) {
            $related = json_decode($product->related_product);
            foreach ($related as $key) {
                $product = Tbl_product::where('id', '=', $key)->first();
                if (isset($product)) {
                    $jsonObject['id'] = $product->id;
                    // $jsonObject['user_id'] = $product->user_id;
                    $jsonObject['name'] = $product->name;
                    $jsonObject['composition'] = self::salts($product->id);
                    $jsonObject['generic_name'] = $product->generic_name;
                    $jsonObject['manufature'] = $product->manufature;
                    $jsonObject['price'] = $product->price;
                    $jsonObject['discount'] = $product->discount;
                    if ($product->discount != 0) {
                        $jsonObject['discount_price'] = $row->price - $product->price * $product->discount / 100;
                    } else {
                        $jsonObject['discount_price'] = $product->price;
                    }
                    $jsonObject['batch_no'] = $product->batch_no;
                    $jsonObject['expiration_date'] = $product->expiration_date;
                    $jsonObject['video_url'] = $product->video_url;
                    $jsonObject['prescription_required'] = $product->prescription_required;
                    $jsonObject['categories'] = $product->categories;
                    $jsonObject['status'] = $product->status;
                    $jsonObject['qty'] = $product->qty;
                    // $jsonObject['slug'] = $product->slug;
                    if ($product->description == null) {
                        $jsonObject['description'] = '';
                    } else {
                        $jsonObject['description'] = $product->description;
                    }
                    if ($product->pro_img != '') {
                        $jsonObject['image'] = [];
                        $jsonObject['image'][] = $product->pro_img;
                    } else {
                        $jsonObject['image'] = self::pro_images($product->id);
                    }
                    if (trim($product->product_form) == 'Capsule' || trim($product->product_form) == 'Tablet') {
                        $form = 'Strip';
                    } else {
                        $form = trim($product->product_form);
                    }
                    $jsonObject['package_per_unit'] = trim($form).' of '.trim($product->package_per_unit);
                    $jsonObject['stock_manage'] = $product->stock_manage;

                    $jsonArray1['items'][] = $jsonObject;
                }
            }
        }
        $jsonArray1['name'] = 'Related Product';
        $jsonArray['product'][] = $jsonArray1;

        // $salt = Tbl_product_salt::where('product_id',$id)->orderby('salt_id','ASC')->get();

        $substi = [];
        $jsonArray['substitutes'] = $substi;

        return $jsonArray;
        // return $jsonObject;
    }

    public static function getsubstitutes($id, $limit)
    {
        $salt = Tbl_product_salt::where('product_id', $id)->orderby('salt_id', 'ASC')->get();

        $substi = [];
        if (isset($salt)) {
            foreach ($salt as $row) {
                $s1['salt_id'] = $row->salt_id;
                $s1['value'] = $row->value;
                $s1['unit'] = $row->unit;

                $c1[] = $s1;
                $product = Tbl_product_salt::where('product_id', '!=', $id)
                    ->where('salt_id', $row->salt_id)
                    ->where('value', $row->value)
                    ->where('unit', $row->unit)
                    ->get();
                foreach ($product as $key) {
                    $jsonArray2[] = $key->product_id;
                }
            }
            if (isset($jsonArray2)) {
                foreach ($jsonArray2 as $r) {
                    $c2 = [];
                    $salt_data = Tbl_product_salt::where('product_id', $r)->orderby('salt_id', 'ASC')->get();

                    foreach ($salt_data as $row) {
                        $s2['salt_id'] = $row->salt_id;
                        $s2['value'] = $row->value;
                        $s2['unit'] = $row->unit;

                        $c2[] = $s2;
                    }
                    // /return $c1;
                    if (count($c1) == count($c2)) {
                        $a = 0;
                        for ($i = 0; $i < count($c2); $i++) {

                            if ($c2[$i]['salt_id'] == $c1[$i]['salt_id'] && $c2[$i]['value'] == $c1[$i]['value'] && $c2[$i]['unit'] == $c1[$i]['unit']) {

                                $a = $a + 1;
                            }
                        }
                        if ($a == count($c1)) {
                            $pr[] = $r;
                        }
                    }
                }
            }
            if (isset($pr)) {
                array_unique($pr);
                $substitutes = Tbl_product::whereIn('id', $pr)
                    ->where('status', 1)
                    ->orderby('price', 'ASC')
                    ->offset($limit)
                    ->limit(10)
                    ->get();
                foreach ($substitutes as $row) {
                    if (isset($row)) {
                        $jsonObject['id'] = $row->id;
                        $jsonObject['name'] = $row->name;
                        $jsonObject['composition'] = self::salts($row->id);
                        $jsonObject['generic_name'] = $row->generic_name;
                        $jsonObject['manufature'] = $row->manufature;
                        $jsonObject['batch_no'] = $row->batch_no;
                        $jsonObject['price'] = $row->price;
                        $jsonObject['discount'] = $row->discount;
                        if ($row->discount != 0) {
                            $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                        } else {
                            $jsonObject['discount_price'] = $row->price;
                        }
                        $jsonObject['expiration_date'] = $row->expiration_date;
                        $jsonObject['video_url'] = $row->video_url;
                        $jsonObject['prescription_required'] = $row->prescription_required;
                        $jsonObject['categories'] = $row->categories;
                        $jsonObject['status'] = $row->status;
                        $jsonObject['qty'] = $row->qty;
                        if ($row->pro_img != '') {
                            $jsonObject['image'] = [];
                            $jsonObject['image'][] = $row->pro_img;
                        } else {
                            $jsonObject['image'] = self::pro_images($row->id);
                        }
                        // $jsonObject['slug'] = $row->slug;
                        $jsonObject['description'] = $row->description;
                        if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                            $form = 'Strip';
                        } else {
                            $form = trim($row->product_form);
                        }
                        $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);

                        $substi[] = $jsonObject;
                    }
                }
            }
        }

        return $jsonArray['substitutes'] = $substi;
    }

    public static function get_salt_details($salt_id)
    {
        $salt = Tbl_salt::where('id', $salt_id)->first();
        // return $salt;
        $array['salt_id'] = $salt->id;
        $array['name'] = $salt->name;
        $desc1['name'] = 'Side Effect';
        $desc1['description'] = $salt->side_effect;

        $desc2['name'] = 'indication';
        $desc2['description'] = $salt->indication;

        $desc3['name'] = 'Contra Indication';
        $desc3['description'] = $salt->contra_indication;

        $desc4['name'] = 'Caution';
        $desc4['description'] = $salt->caution;

        $array['salt'][] = $desc1;
        $array['salt'][] = $desc2;
        $array['salt'][] = $desc3;
        $array['salt'][] = $desc4;

        return $array;
    }

    public static function search_list($search, $limit)
    {
        $jsonArray = [];
        //    $product = Tbl_product::where(function ($query)  use ($search) {
        //       $query->orWhere('name','like',"{$search}%");
        //   })
        $product = Tbl_product::where('name', 'like', "%{$search}%")
            ->where('status', 1)
                                // ->orderby('is_drug','DESC')
            ->offset($limit)
            ->limit(10)
            ->get();

        // dd($product);

        if (isset($product)) {
            foreach ($product as $row) {

                $jsonObject['id'] = $row->id;
                // $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['composition'] = self::salts($row->id);
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['price'] = $row->price;
                $jsonObject['discount'] = $row->discount;
                if ($row->discount != 0) {
                    $jsonObject['discount_price'] = $row->price - $row->price * $row->discount / 100;
                } else {
                    $jsonObject['discount_price'] = $row->price;
                }
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['qty'] = $row->qty;
                if ($row->pro_img != '') {
                    $jsonObject['image'] = [];
                    $jsonObject['image'][] = $row->pro_img;
                } else {
                    $jsonObject['image'] = self::pro_images($row->id);
                }
                if (trim($row->product_form) == 'Capsule' || trim($row->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($row->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($row->package_per_unit);
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function auto_suggestion_list($search)
    {
        $jsonArray = [];
        //   $product = Tbl_product::where(function ($query)  use ($search) {
        //       $query->orWhere('name','like',"{$search}%");
        //   })
        $product = Tbl_product::where('name', 'like', "%{$search}%")
            ->where('status', 1)
            ->orderBy('is_drug', 'DESC')
            ->limit(20)
            ->get();

        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['user_id'] = $row->user_id;
                $jsonObject['name'] = $row->name;
                $jsonObject['generic_name'] = $row->generic_name;
                $jsonObject['manufature'] = $row->manufature;
                $jsonObject['batch_no'] = $row->batch_no;
                $jsonObject['discount'] = $row->discount;
                $jsonObject['expiration_date'] = $row->expiration_date;
                $jsonObject['video_url'] = $row->video_url;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['categories'] = $row->categories;
                $jsonObject['status'] = $row->status;
                $jsonObject['price'] = $row->price;
                $jsonObject['qty'] = $row->qty;
                // $jsonObject['unit'] = $row->unit;
                if (strpos($row->unit, 'Tablet') !== false) {
                    str_replace('Tablet', 'Strip', $row->unit);
                }
                $jsonObject['package_per_unit'] = $row->unit.' of '.$row->package_per_unit;

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function address_list($id)
    {
        $jsonArray = [];
        $address = Tbl_address_customer::where('customer_id', '=', $id)->get();
        if (isset($address)) {
            foreach ($address as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['customer_id'] = $row->customer_id;
                $jsonObject['first_name'] = $row->first_name;
                $jsonObject['last_name'] = $row->last_name;
                $jsonObject['email'] = $row->email;
                $jsonObject['phone'] = $row->phone;
                $jsonObject['address'] = $row->address;
                $jsonObject['type'] = $row->type;
                $jsonObject['country'] = $row->country;
                $jsonObject['state'] = $row->state;
                $jsonObject['city'] = $row->city;
                $jsonObject['zip'] = $row->zip;
                $jsonObject['alternativ_phone'] = ($row->alternativ_phone == null) ? '' : $row->alternativ_phone;
                $jsonObject['landmark'] = $row->landmark;
                $jsonObject['lat'] = $row->lat;
                $jsonObject['lang'] = $row->lang;

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function address_details($customer_id, $address_id)
    {
        $jsonArray = [];
        $address = Tbl_address_customer::where('id', '=', $address_id)
            ->where('customer_id', '=', $customer_id)
            ->first();
        if (isset($address)) {
            $jsonObject['id'] = $address->id;
            $jsonObject['customer_id'] = $address->customer_id;
            $jsonObject['first_name'] = $address->first_name;
            $jsonObject['last_name'] = $address->last_name;
            $jsonObject['email'] = $address->email;
            $jsonObject['phone'] = $address->phone;
            $jsonObject['address'] = $address->address;
            $jsonObject['type'] = $address->type;
            $jsonObject['country'] = $address->country;
            $jsonObject['state'] = $address->state;
            $jsonObject['city'] = $address->city;
            $jsonObject['zip'] = $address->zip;
            $jsonObject['alternativ_phone'] = $address->alternativ_phone;
            $jsonObject['landmark'] = $address->landmark;
            $jsonObject['lat'] = $address->lat;
            $jsonObject['lang'] = $address->lang;

            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function coupon_details($coupon_id)
    {
        $jsonArray = [];
        $coupon = Tbl_coupon::where('id', '=', $coupon_id)->first();
        if (isset($coupon)) {
            $jsonObject['id'] = $coupon->id;
            $jsonObject['code'] = $coupon->code;
            $jsonObject['discount_type'] = $coupon->discount_type;
            $jsonObject['value'] = $coupon->value;
            $jsonObject['minimum_cart_amount'] = $coupon->minimum_cart_amount;
            $jsonObject['maximun_spend'] = $coupon->maximun_spend;
            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function list_of_img_prescription($user_id)
    {
        $jsonArray = [];
        $order_id = [];
        $img = Tbl_img_prescription::where('customer_id', $user_id)->where('type', 1)->get();
        foreach ($img as $row) {
            $order_id[] = $row->order_id;
        }
        // return $order_id;
        $order = array_unique($order_id);
        foreach ($order as $row) {

            $final = Tbl_img_prescription::where('customer_id', $user_id)->where('order_id', $row)->get();
            $jsonObject['id'] = $row;
            $jsonObject['image'] = [];

            foreach ($final as $f) {
                $jsonObject['date'] = date('d-m-Y', strtotime($f->created_at));
                $Object['id'] = $f->id;
                $Object['img'] = asset('public/images/prescription_images/'.$f->image);
                $jsonObject['image'][] = $Object;
            }
            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function prescription_img($user_id, $image_id)
    {
        $img = Tbl_img_prescription::where('id', $image_id)->where('customer_id', $user_id)->first();

        if (isset($img)) {
            $jsonObject['id'] = $img->id;
            $jsonObject['title'] = $img->title;
            $jsonObject['image'] = asset('public/images/prescription_images/'.$img->image);
        }

        return $jsonObject;
    }

    public static function pending_order($user_id, $limit)
    {
        $jsonArray = [];
        $order = Tbl_order::where('customer_id', $user_id)
            ->where('order_type', 0)
            ->where('order_status', '!=', 'Delivered')
            ->orderby('id', 'DESC')
            ->offset($limit)
            ->limit(10)
            ->get();
        if (isset($order)) {
            foreach ($order as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['order_number'] = $row->order_number;
                $jsonObject['order_type'] = $row->order_type;
                $jsonObject['sub_total'] = $row->sub_total;
                $jsonObject['discount'] = $row->discount;
                $jsonObject['order_status'] = $row->order_status;
                $jsonObject['payment_status'] = $row->payment_status;
                $jsonObject['grand_total'] = $row->grand_total;
                if ($row->self_pickup == 1) {
                    $jsonObject['self_pickup'] = 1;
                } else {
                    $jsonObject['self_pickup'] = 0;
                }
                $pro = Tbl_order_product::where('order_id', $row->id)->first();
                if (isset($pro)) {
                    $jsonObject['product'] = $pro->pro_name;
                }
                // $jsonObject['product'] = $pro->pro_name;
                $jsonObject['created_at'] = date('d-m-Y', strtotime($row->created_at));

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function complete_order($user_id, $limit)
    {
        $jsonArray = [];
        $order = Tbl_order::where('customer_id', $user_id)
            ->where('order_type', 0)
            ->where('order_status', 'Delivered')
            ->offset($limit)
            ->limit(10)
            ->get();
        if (isset($order)) {
            foreach ($order as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['order_number'] = $row->order_number;
                $jsonObject['order_type'] = $row->order_type;
                $jsonObject['sub_total'] = $row->sub_total;
                $jsonObject['discount'] = $row->discount;
                $jsonObject['order_status'] = $row->order_status;
                $jsonObject['payment_status'] = $row->payment_status;
                $jsonObject['grand_total'] = $row->grand_total;
                if ($row->self_pickup == 1) {
                    $jsonObject['self_pickup'] = 1;
                } else {
                    $jsonObject['self_pickup'] = 0;
                }
                $pro = Tbl_order_product::where('order_id', $row->id)->first();
                $jsonObject['product'] = $pro->pro_name;
                $jsonObject['created_at'] = date('d-m-Y', strtotime($row->created_at));

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function order_details($user_id, $order_id)
    {
        $jsonArray = [];
        $jsonArray['images'] = [];
        $order = Tbl_order::where('id', $order_id)
            ->where('customer_id', $user_id)
            ->first();

        $jsonArray['order_id'] = $order->id;
        $jsonArray['order_status'] = $order->order_status;
        $jsonArray['payment_status'] = $order->payment_status;
        $jsonArray['payment_type'] = $order->payment_type;
        $jsonArray['order_number'] = $order->order_number;
        $jsonArray['name'] = $order->first_name.' '.$order->last_name;
        $jsonArray['phone'] = $order->phone;
        $jsonArray['Address'] = $order->address.','.$order->city.','.$order->state.','.$order->pin_code;
        $jsonArray['order_date'] = date('d-m-Y', strtotime($order->created_at));
        $jsonArray['shipping_price'] = ($order->shipping_price == null) ? 0 : $order->shipping_price;
        if ($order->self_pickup == 1) {
            $jsonArray['self_pickup'] = 1;
        } else {
            $jsonArray['self_pickup'] = 0;
        }
        if ($order->order_date != null) {
            $jsonArray['delivery_date'] = date('d-m-Y', strtotime($order->order_date));
        } else {
            $jsonArray['delivery_date'] = '';
        }
        $jsonArray['sub_total'] = ($order->sub_total == null) ? 0 : $order->sub_total;
        $jsonArray['discount'] = ($order->discount == null) ? 0 : $order->discount;
        if ($order->coupon == null) {
            $jsonArray['coupon'] = '';
        } else {
            $jsonArray['coupon'] = $order->coupon;
        }
        if ($order->discount_type == null) {
            $jsonArray['discount_type'] = '';
        } else {
            $jsonArray['discount_type'] = $order->discount_type;
        }
        $jsonArray['discount_value'] = ($order->discount_value == null) ? 0 : $order->discount_value;
        $jsonArray['price_discount'] = ($order->price_discount == null) ? 0 : $order->price_discount;
        $jsonArray['images'] = self::pre_img($order->id);
        $jsonArray['total_recived'] = $order->total_recived;
        $jsonArray['grand_total'] = ($order->grand_total == null) ? 0 : $order->grand_total;
        $jsonArray['reject_reason'] = '';
        if ($order->reject_reason != null) {
            $jsonArray['reject_reason'] = $order->reject_reason;
        } elseif ($order->cancle_order != null) {
            $jsonArray['reject_reason'] = $order->cancle_order;
        }
        $jsonArray['prescription_order_item'] = [];
        $product = Tbl_order_product::where('order_id', $order_id)->get(); // ->where('prescription_required',1)
        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['pro_id'] = $row->pro_id;
                $jsonObject['pro_name'] = $row->pro_name;
                $jsonObject['pro_sku'] = $row->pro_sku;
                $jsonObject['pro_mrp_price'] = $row->pro_mrp_price;
                $jsonObject['pro_discount_value'] = ($row->pro_discount_value == null) ? '0.0' : $row->pro_discount_value;
                $jsonObject['pro_discount_price'] = ($row->pro_discount_price == null) ? '0.0' : $row->pro_discount_price;
                $jsonObject['pro_price'] = $row->pro_price;
                $jsonObject['pro_qty'] = $row->pro_qty;
                $jsonObject['total_amount'] = $row->total_amount;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['pack_size_label'] = ($row->pack_size_label == null) ? '' : $row->pack_size_label;

                $jsonArray['prescription_order_item'][] = $jsonObject;
            }
        }
        $jsonArray['order_item'] = [];
        $product = Tbl_order_product::where('order_id', $order_id)->where('prescription_required', 0)->get();
        if (isset($product)) {
            foreach ($product as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['pro_id'] = $row->pro_id;
                $jsonObject['pro_name'] = $row->pro_name;
                $jsonObject['pro_sku'] = $row->pro_sku;
                $jsonObject['pro_mrp_price'] = $row->pro_mrp_price;
                $jsonObject['pro_discount_value'] = ($row->pro_discount_value == null) ? '0.0' : $row->pro_discount_value;
                $jsonObject['pro_discount_price'] = ($row->pro_discount_price == null) ? '0.0' : $row->pro_discount_price;
                $jsonObject['pro_price'] = $row->pro_price;
                $jsonObject['pro_qty'] = $row->pro_qty;
                $jsonObject['total_amount'] = $row->total_amount;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $jsonObject['pack_size_label'] = ($row->pack_size_label == null) ? '' : $row->pack_size_label;

                $jsonArray['order_item'][] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function reorder_details($user_id, $order_id)
    {
        $jsonArray = [];
        $jsonArray['images'] = [];
        $order = Tbl_order::where('id', $order_id)->where('customer_id', $user_id)->first();
        $jsonArray['order_id'] = $order->id;
        $jsonArray['order_status'] = $order->order_status;
        $jsonArray['payment_status'] = $order->payment_status;
        $jsonArray['payment_type'] = $order->payment_type;
        $jsonArray['order_number'] = $order->order_number;
        $jsonArray['name'] = $order->first_name.' '.$order->last_name;
        $jsonArray['phone'] = $order->phone;
        $jsonArray['Address'] = $order->address.','.$order->city.','.$order->state.','.$order->pin_code;
        $jsonArray['order_date'] = date('d-m-Y', strtotime($order->created_at));
        $jsonArray['shipping_price'] = '0';
        $jsonArray['self_pickup'] = ($order->self_pickup == 1) ? 1 : 0;
        $jsonArray['delivery_date'] = date('d-m-Y', strtotime($order->order_date));
        $jsonArray['sub_total'] = 0;
        $jsonArray['discount'] = (float) 0;
        $jsonArray['coupon'] = '';
        $jsonArray['discount_type'] = '';
        $jsonArray['discount_value'] = '0';
        $jsonArray['price_discount'] = '0';
        $jsonArray['images'] = self::pre_img($order->id);
        $jsonArray['grand_total'] = '0';
        $jsonArray['prescription_order_item'] = [];
        $product = Tbl_order_product::where('order_id', $order_id)->get(); // ->where('prescription_required',1)
        if (isset($product)) {
            foreach ($product as $row) {
                $product_detail = Tbl_product::where('id', $row->pro_id)->where('status', 1)->first();
                if (isset($product_detail)) {
                    $jsonObject['id'] = $row->id;
                    $jsonObject['pro_id'] = $product_detail->id;
                    $jsonObject['pro_name'] = $product_detail->name;
                    $jsonObject['pro_sku'] = '';
                    $jsonObject['pro_mrp_price'] = (string) $product_detail->price;
                    $jsonObject['pro_discount_value'] = (string) 0;
                    $jsonObject['pro_discount_price'] = (string) 0;
                    if ($product_detail->discount != 0) {
                        $jsonObject['pro_discount_value'] = (string) $product_detail->discount;
                        $jsonObject['pro_discount_price'] = (string) ($product_detail->price * $product_detail->discount / 100);
                    }
                    $jsonObject['pro_price'] = (string) ($jsonObject['pro_mrp_price'] - $jsonObject['pro_discount_price']);
                    $jsonObject['pro_qty'] = $row->pro_qty;
                    $jsonObject['total_amount'] = (string) ($jsonObject['pro_price'] * $jsonObject['pro_qty']);
                    $jsonObject['prescription_required'] = $product_detail->prescription_required;
                    $jsonObject['pack_size_label'] = $row->pack_size_label;

                    $jsonArray['sub_total'] = $jsonArray['sub_total'] + ($jsonObject['pro_mrp_price'] * $row->pro_qty);
                    $jsonArray['price_discount'] = $jsonArray['price_discount'] + $jsonObject['pro_discount_price'];
                    $jsonArray['grand_total'] = $jsonArray['grand_total'] + $jsonObject['total_amount'];
                    $jsonArray['prescription_order_item'][] = $jsonObject;
                }
            }
        }
        $jsonArray['grand_total'] = (string) $jsonArray['grand_total'];
        $jsonArray['price_discount'] = (string) $jsonArray['price_discount'];
        $jsonArray['sub_total'] = (string) $jsonArray['sub_total'];

        $jsonArray['order_item'] = [];

        return $jsonArray;
    }

    public static function get_req_list($id, $limit)
    {
        $jsonArray = [];
        $order = Tbl_order::where('customer_id', $id)->where('order_type', '1')
            ->orderby('id', 'DESC')
            ->offset($limit)
            ->limit(10)
            ->get();
        if (! empty($order)) {
            foreach ($order as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['order_number'] = $row->order_number;
                $jsonObject['customer_id'] = $row->customer_id;
                $jsonObject['order_status'] = $row->order_status;
                $jsonObject['description'] = $row->description;
                $jsonObject['created_at'] = date('d-m-Y', strtotime($row->created_at));

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function get_req_details($customer_id, $id)
    {
        $jsonArray = [];
        $order = Tbl_order::where('customer_id', $customer_id)->where('id', $id)->first();
        if (! empty($order)) {
            $jsonObject['id'] = $order->id;
            $jsonObject['order_number'] = $order->order_number;
            $jsonObject['customer_id'] = $order->customer_id;
            $jsonObject['order_status'] = $order->order_status;
            $jsonObject['description'] = $order->description;
            $jsonObject['images'] = self::pre_img($order->id);
            $jsonObject['medicine'] = self::medicine_list($order->id);
            $jsonObject['created_at'] = date('d-m-Y', strtotime($order->created_at));

            $jsonArray[] = $jsonObject;
        }

        return $jsonArray;
    }

    public static function pre_img($order_id)
    {
        $jsonObject = [];
        $img = Tbl_img_prescription::where('order_id', $order_id)->get();
        if (! empty($img)) {
            foreach ($img as $row) {
                $jsonObject[] = asset('public/images/prescription_images/'.$row->image);
            }
        }

        return $jsonObject;
    }

    public static function medicine_list($order_id)
    {
        $jsonArray = [];
        $med = Tbl_order_product::where('order_id', $order_id)->get();
        if (! empty($med)) {
            foreach ($med as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['pro_id'] = $row->pro_id;
                $jsonObject['pro_name'] = $row->pro_name;
                $jsonObject['pro_sku'] = $row->pro_sku;
                $jsonObject['pro_mrp_price'] = $row->pro_mrp_price;
                if ($row->pro_discount_value == null) {
                    $jsonObject['pro_discount_value'] = 0;
                } else {
                    $jsonObject['pro_discount_value'] = $row->pro_discount_value;
                }
                $jsonObject['pro_discount_price'] = $row->pro_discount_price;
                $jsonObject['pro_price'] = $row->pro_price;
                $jsonObject['pro_description'] = $row->pro_description;
                $jsonObject['pro_qty'] = $row->pro_qty;
                $jsonObject['total_amount'] = $row->total_amount;
                $jsonObject['prescription_required'] = $row->prescription_required;
                $pro = Tbl_product::where('id', $row->pro_id)->first();
                if (trim($pro->product_form) == 'Capsule' || trim($pro->product_form) == 'Tablet') {
                    $form = 'Strip';
                } else {
                    $form = trim($pro->product_form);
                }
                $jsonObject['package_per_unit'] = trim($form).' of '.trim($pro->package_per_unit);
                $jsonObject['manufacturar'] = Tbl_manufature::where('id', $pro->manufature)->first()->name;
                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }

    public static function get_noti_list($customer_id, $limit)
    {
        $jsonArray = [];
        $noti = Tbl_notification::where('customer_id', $customer_id)
            ->orderby('id', 'DESC')
            ->offset($limit)
            ->limit(10)
            ->get();
        if (! empty($noti)) {
            foreach ($noti as $row) {
                $jsonObject['id'] = $row->id;
                $jsonObject['order_id'] = $row->order_id;
                $jsonObject['notification'] = $row->notification;
                $jsonObject['title'] = $row->title;
                $jsonObject['notification_type'] = $row->notification_type;
                $jsonObject['created_at'] = date('d-m-Y', strtotime($row->created_at));

                $jsonArray[] = $jsonObject;
            }
        }

        return $jsonArray;
    }
}
