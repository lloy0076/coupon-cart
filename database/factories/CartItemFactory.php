<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CartItem;
use Faker\Generator as Faker;

$factory->define(CartItem::class, function (Faker $faker) {
    return [
        'quantity' => 0,
        'price_inc' => 0,
    ];
});
