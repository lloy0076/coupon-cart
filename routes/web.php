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
    'middleware' => ['web'],
],
    function () {
        $roleString = sprintf('role:%s', Constants::ROLE_ADMIN);

        Route::get('/',
            function () {
                return view('welcome');
            });

        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/coupons', 'HomeController@coupons')->middleware($roleString)->name('coupons');
    });
