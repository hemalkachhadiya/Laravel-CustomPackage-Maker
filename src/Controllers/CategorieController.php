<?php

namespace Smarttech\Prod\Controllers;

use Illuminate\Http\Request;
use Smarttech\Prod\Controllers\BaseController as BaseController;

class CategorieController extends BaseController
{
    public function __construct() {}

    public function categorie_list(Request $request)
    {
        if (empty($request->categorie_id)) {
            $data = CommonModal::categorie_list();
        } else {
            $data = CommonModal::categorie_detail($request->categorie_id);
        }
        if (count($data) > 0) {
            return $this->sendResponse($data, 'List load successfully.');
        } else {
            return $this->sendError('List not found', '');
        }
    }
}
