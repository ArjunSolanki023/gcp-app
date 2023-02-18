<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::resource('category', CategoryController::class);
Route::get('products', [ProductController::class , 'productList']);
Route::middleware('auth:api')->group( function () {
    Route::post('changePassword', [PassportAuthController::class, 'changePassword']);
    Route::post('logout', [PassportAuthController::class, 'logout']);
    Route::get('getUserProfile', [PassportAuthController::class, 'getUserProfile']);
    Route::put('profileUpdate/{id}/', [PassportAuthController::class, 'profileUpdate']);
    Route::get('getUserProfile', [PassportAuthController::class, 'getUserProfile']);
    Route::post("addToCart", [ProductController::class, 'addToCart']);
    Route::get("cartList", [ProductController::class, 'cartList']);
    Route::post("addOrder", [ProductController::class, 'addOrder']);
    Route::post("addAddress", [ProductController::class, 'addAddress']);
    Route::post("itemRemovefromCart",[ProductController::class, 'itemRemovefromCart']);
});
Route::get('autherror',function (){
    $response = ["error"=>1, "message" => trans('Your session is expired. please login again.')];
    return response($response, 401);
})->name('e404error');
