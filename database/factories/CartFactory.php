<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Cart;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Cart::class,
    function (Faker $faker) {
        return [
            'order_id'       => Str::uuid(),
            'user_id'        => 0,
            'submitted_date' => null,
            'instructions'   => $faker->paragraph(2, true),
            'coupon_id'      => null,
            'total_inc'      => 0,
        ];
    });
