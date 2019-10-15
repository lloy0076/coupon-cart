<?php

use App\Models\Coupon;
use App\Models\CouponRule;
use Illuminate\Database\Seeder;

class CouponRulesAndDiscountsSeeder extends Seeder
{
    /*
     * We'll create two carts:
     *
     * 1. One with two items;
     * 2. One with no items.
     */
    public function run()
    {
        $coupon = factory(Coupon::class)->create([
            'coupon_code' => 'FIXED10',
            'display_name' => 'Fixed 10',
        ]);

        $couponRules = [
            ['description' => 'Coupon Rule 1', 'rule' => '"one" == "one"', 'rule_type' => CouponRule::RULE_COUPON],
            CouponRule::makeNewRule(['description' => 'Coupon Rule 2', 'rule_type' => CouponRule::RULE_COUPON]),
            CouponRule::makeNewCouponRule(['description' => 'Coupon Rule 3', 'rule_type' => CouponRule::RULE_DISCOUNT]),
        ];

        $coupon->addRules($couponRules);

        $discountRules = [
            ['description' => 'Discount Rule 1', 'rule' => '100', 'rule_type' => CouponRule::RULE_DISCOUNT],
            CouponRule::makeNewRule([
                'description' => 'Discount Rule 2',
                'rule'        => '50',
                'rule_type'   => CouponRule::RULE_DISCOUNT,
            ]),
            CouponRule::makeNewDiscountRule([
                'description' => 'Discount Rule 3',
                'rule'        => '25',
                'rule_type'   => CouponRule::RULE_COUPON,
            ]),
        ];

        $coupon->addRules($discountRules);
    }
}
