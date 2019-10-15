<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CouponRule;
use Faker\Generator as Faker;

$factory->define(CouponRule::class, function (Faker $faker) {
    return [
        'coupon_id' => 0,
        'rule' => 'true == true', // Always return true.
        'rule_type' => CouponRule::RULE_COUPON,
        'description' => $faker->numerify("Always return true ###"),
        'rule_order' => 1,
        'rule_not' => false,
        'rule_operator' => 'and',
    ];
});
