<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Product::class,
    function (Faker $faker) {
        return [
            'name'            => $faker->unique()->word,
            'sku'             => (string)Str::uuid(),
            'price'           => $faker->numberBetween(0, 1000),
            'description'     => $faker->paragraphs(2, true),
            'image'           => $faker->imageUrl(200, 200, 'cats'),
        ];
    });
