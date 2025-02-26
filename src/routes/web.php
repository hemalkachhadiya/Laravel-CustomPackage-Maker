<?php

namespace Smarttech\Prod;

use Illuminate\Support\Facades\Route;
use Smarttech\Prod\Controllers\CategorieController;
use Smarttech\Prod\Controllers\ProductController;
use Smarttech\Prod\Controllers\OrderController;

// use Smarttech\Prod\Controllers\ProductController;
//
// dd('cate');
Route::get('product_list', [ProductController::class, 'product_list']);
Route::post('/categorie_list', [CategorieController::class, 'categorie_list']);



Route::post('/place_order', [OrderController::class,'place_order']);
Route::post('/re_order',[OrderController::class,'re_order']);
Route::post('/check_coupon_code',[OrderController::class,'check_coupon']);
Route::post('/pending_order',[OrderController::class,'pending_order']);
Route::post('/complete_order',[OrderController::class,'complete_order']);
Route::post('/order_details', [OrderController::class,'order_details']);
Route::post('/cancle_order', [OrderController::class,'cancle_order']);
Route::post('/order_tracking', [OrderController::class,'order_tracking']);
Route::post('/place_order_with_prescription', [OrderController::class,'place_order_with_prescription']);

// Route::prefix('products')->group(function () {
//     Route::get('/', [ProductController::class, 'index'])->name('products.index');
//     Route::get('/create', [ProductController::class, 'create'])->name('products.create');
//     Route::post('/', [ProductController::class, 'store'])->name('products.store');
//     Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
//     Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
//     Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
// });
