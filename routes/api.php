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
        Route::resource('products', 'ProductController');
    });

Route::group(
    [
        'middleware' => ['auth:api', $roleString, 'cart_create'],
    ],
    function () {
        Route::resource('coupons', 'CouponController');
        Route::resource('couponRules', 'CouponRuleController');
    }
);
