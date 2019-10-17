<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Constants;

Auth::routes();

Route::group([
    'middleware' => ['web', 'cart_create'],
],
    function () {
        $roleString = sprintf('role:%s', Constants::ROLE_ADMIN);

        Route::get('/', 'HomeController@index');
        Route::get('/home', 'HomeController@index')->name('web.home');
        Route::resource('products', 'ProductController');

        // These need admin rights.
        Route::get('/coupons', 'HomeController@coupons')->middleware($roleString)->name('web.coupons');
    });
