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
        $this->makeFixed10();
        $this->makePercent10();
        $this->makeMixed10();
        $this->makeRejected10();
    }

    /**
     * Make the FIXED10 coupon.
     */
    protected function makeFixed10(): void
    {
        $coupon = factory(Coupon::class)->create([
            'coupon_code'     => 'FIXED10',
            'display_name'    => 'Fixed 10',
            'stop_processing' => true,
        ]);

        $couponRules = [
            [
                'description' => 'More Than One Item',
                'rule'        => 'cart.numberOfCartItems() > 0',
                'rule_order'  => 2,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
            [
                'description' => 'Greater Than $50',
                'rule'        => 'cart.grossCartCost() > 50',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
        ];

        $coupon->addRules($couponRules);

        $discountRules = [
            [
                'description' => 'Ten Dollar Off',
                'rule'        => '"$10"',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_DISCOUNT,
            ],
        ];

        $coupon->addRules($discountRules);
    }

    /**
     * Make the percent 10 coupon.
     */
    protected function makePercent10(): void
    {
        $coupon = factory(Coupon::class)->create([
            'coupon_code'     => 'PERCENT10',
            'display_name'    => 'Percent 10',
            'stop_processing' => true,
        ]);

        $couponRules = [
            [
                'description' => 'More Than Two Items',
                'rule'        => 'cart.numberOfCartItems() > 1',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
            [
                'description' => 'Greater Than $100',
                'rule'        => 'cart.grossCartCost() > 100',
                'rule_order'  => 2,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
        ];

        $coupon->addRules($couponRules);

        $discountRules = [
            [
                'description' => 'Ten Percent Off',
                'rule'        => '"%10"',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_DISCOUNT,
            ],
        ];

        $coupon->addRules($discountRules);
    }

    /**
     * Make the mixed 10 coupon.
     *
     * This rule makes no sense; 10% of $200 will always be > $10; I assume this is here for testing purposes.
     */
    protected function makeMixed10(): void
    {
        $coupon = factory(Coupon::class)->create([
            'coupon_code'     => 'MIXED10',
            'display_name'    => 'Mixed 10',
            'stop_processing' => true,
        ]);

        $couponRules = [
            [
                'description' => 'Greater Than $200',
                'rule'        => 'cart.grossCartCost() > 200',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
            [
                'description' => 'More Than One Item',
                'rule'        => 'cart.numberOfCartItems() > 2',
                'rule_order'  => 2,
                'rule_type'   => CouponRule::RULE_COUPON,
            ],
        ];

        $coupon->addRules($couponRules);

        $discountRules = [
            [
                'description' => 'Greater of 10% or 10$',
                'rule'        => 'cart.grossCartCost() * 0.1 > 10 ? "%10" : "$10"',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_DISCOUNT,
            ],
        ];

        $coupon->addRules($discountRules);
    }

    /**
     * Make the rejected 10 coupon.
     */
    protected function makeRejected10(): void
    {
        $coupon = factory(Coupon::class)->create([
            'coupon_code'     => 'REJECTED10',
            'display_name'    => 'Rejected 10',
            'stop_processing' => true,
        ]);

        $couponRules = [
            [
                'description' => 'Greater Than $1000',
                'rule'        => 'cart.grossCartCost() > 1000',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_COUPON,
            ]
        ];

        $coupon->addRules($couponRules);

        $discountRules = [
            [
                'description' => '$10 Off then 10% Off',
                'rule'        => '"$10;%10"',
                'rule_order'  => 1,
                'rule_type'   => CouponRule::RULE_DISCOUNT,
            ],
        ];

        $coupon->addRules($discountRules);
    }
}
