<?php

use App\Constants;
use Illuminate\Http\Request;

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

$roleString = sprintf('role:%s', Constants::ROLE_ADMIN);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        'middleware' => ['auth:api', 'cart_create'],
    ],
    function () {
        Route::get('cartOp/applyCoupon/{coupon}', 'CartController@applyCoupon');
        Route::post('cartOp/finaliseCart/{cart}', 'CartController@finaliseCart');
        Route::get('cartOp/removeCoupons', 'CartController@removeCoupons');
        Route::post('cartOp/{product}/{quantity?}', 'CartController@addItem');
        Route::delete('cartOp/{cartItem}', 'CartController@removeItem');

        Route::resource('carts', 'CartController');
        Route::resource('products', 'ProductController');

        Route::get('coupons/byCouponCode', 'CouponController@getByCouponCode');
        Route::resource('coupons', 'CouponController')->only(['show']);
    });

Route::group(
    [
        'middleware' => ['auth:api', $roleString, 'cart_create'],
    ],
    function () {
        Route::resource('coupons', 'CouponController')->except(['show']);
        Route::resource('couponRules', 'CouponRuleController');
    }
);
