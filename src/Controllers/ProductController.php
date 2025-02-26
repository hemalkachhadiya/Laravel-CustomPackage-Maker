<?php

namespace Smarttech\Prod\Controllers;

// use DB;
use Illuminate\Http\Request;
// use Validator;
// use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Smarttech\Prod\Controllers\BaseController as BaseController;
use Smarttech\Prod\Models\Tbl_image;
use Smarttech\Prod\Models\Tbl_product;
use Smarttech\Prod\Models\Tbl_product_image;

class ProductController extends BaseController
{
    public function __construct() {}

    public function product_list(Request $request)
    {
        // $rules = [
        //     'limit'=>'required',
        // ];
        // $validator = Validator::make( $request->all(), $rules );
        // if ( $validator->fails() ) {
        //     $errorString = implode( ',', $validator->messages()->all() );
        //     return $this->sendError( $errorString, '' );
        // }

        if (empty($request->categorie_id)) {
            $jsonArray = [];
            $product = Tbl_product::where('status', 1)
                ->offset($request->limit)
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
            $data = $jsonArray;
            // $data = CommonModal::product_list( $request->limit );
        } else {
            $jsonArray = [];

            // $product = Tbl_product::whereRaw('json_contains(categories, \'["' . $category . '"]\')')
            //                         ->offset($limit)
            //                         ->limit(10)
            //                         ->get();

            $categories = DB::select('SELECT * FROM tbl_categories WHERE id = "'.$request->categorie_id.'" OR id IN (SELECT id FROM tbl_categories WHERE categories_id = "'.$request->categorie_id.'")');
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
            $product = DB::select("SELECT * FROM tbl_products WHERE status = '1' AND (".implode(' OR ', $sql).') limit '.$request->limit.',10');

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
            $data = $jsonArray;
            // $data =CommonModal::product_list_by_categorie( 2, $request->categorie_id );
        }
        $offset = $request->limit + 10;
        if ($request->wantsJson()) {
            if (count($data) > 0) {
                // return response()->json([
                //     'data' => $data,
                //     'message' => 'List Loaded Successfully',
                //     'offset' => $offset
                // ], 200);
                return $this->sendResponsePagination($data, 'List load successfully.', $offset);
            } else {
                return $this->sendError('List not found', '');
            }
        }

        return view('furniProd', compact('data', 'offset'));
    }

    // this function is used to
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
}
