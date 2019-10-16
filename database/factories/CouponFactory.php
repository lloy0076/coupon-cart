<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Coupon;
use Faker\Generator as Faker;

$factory->define(Coupon::class, function (Faker $faker) {
    $number = $faker->unique()->numerify('####');
    
    return [
        'coupon_id' => \Illuminate\Support\Str::uuid(),
        'coupon_code' => "Code $number",
        'display_name' => "Rule $number",
        'order' => 1,
        'stop_processing' => true,
        'rule_group' => Coupon::RULE_GROUP_DEFAULT,
    ];
});
