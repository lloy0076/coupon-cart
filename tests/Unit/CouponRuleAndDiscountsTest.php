<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\CouponRule;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CouponRuleAndDiscountsTest extends TestCase
{
    use DatabaseMigrations;

    public function testCouponRules()
    {
        $this->seed();

        $coupon = Coupon::first();
        $this->assertIsObject($coupon, "It is a coupon");

        $rules = $coupon->couponRules();
        $rules = $rules->get();

        $this->assertNotNull($rules, "There are some rules");
        $this->assertCount(3, $rules, "There should be 3 rules");

        // Tests rules made by hash only.
        $this->assertEquals("Coupon Rule 1", $rules[0]->description, "Its description should be 'Coupon Rule 1'");
        $this->assertEquals('"one" == "one"', $rules[0]->rule, "Its rule should be \"one\" == \"one\"");
        $this->assertEquals(CouponRule::RULE_COUPON, $rules[0]->rule_type, "It should be a coupon rule");

        // Test rules made via "new rule".
        $this->assertEquals("Coupon Rule 2", $rules[1]->description, "Its description should be 'Coupon Rule 2'");
        $this->assertEquals('true == true', $rules[1]->rule, "Its rule should be 'true' == 'true'");
        $this->assertEquals(CouponRule::RULE_COUPON, $rules[1]->rule_type, "It should be a coupon rule");


        // Tests a rule made via MakeNewCouponRule with odd/wrong rule type (it should still come out as a coupon rule).
        $this->assertEquals("Coupon Rule 3", $rules[2]->description, "Its description should be 'Coupon Rule 3'");
        $this->assertEquals('true == true', $rules[2]->rule, "Its rule should be 'true' == 'true'");
        $this->assertEquals(CouponRule::RULE_COUPON, $rules[2]->rule_type, "It should be a coupon rule");
    }

    public function testDiscountRules()
    {
        $this->seed();

        $coupon = Coupon::first();
        $this->assertIsObject($coupon, "It is a coupon");

        $rules = $coupon->discountRules();
        $rules = $rules->get();

        $this->assertNotNull($rules, "There are some rules");
        $this->assertCount(3, $rules, "There should be 3 rules");

        // Tests rules made by hash only.
        $this->assertEquals("Discount Rule 1", $rules[0]->description, "Its description should be 'Discount Rule 1'");
        $this->assertEquals("100", $rules[0]->rule, "Its rule should be '100'");
        $this->assertEquals(CouponRule::RULE_DISCOUNT, $rules[0]->rule_type, "It should be a coupon rule");

        // Test rules made via "new rule".
        $this->assertEquals("Discount Rule 2", $rules[1]->description, "Its description should be 'Discount Rule 2'");
        $this->assertEquals("50", $rules[1]->rule, "Its rule should be '50'");
        $this->assertEquals(CouponRule::RULE_DISCOUNT, $rules[1]->rule_type, "It should be a coupon rule");


        // Tests a rule made via MakeNewCouponRule with odd/wrong rule type (it should still come out as a coupon rule).
        $this->assertEquals("Discount Rule 3", $rules[2]->description, "Its description should be 'Discount Rule 3'");
        $this->assertEquals("25", $rules[2]->rule, "Its rule should be '25'");
        $this->assertEquals(CouponRule::RULE_DISCOUNT, $rules[2]->rule_type, "It should be a coupon rule");
    }
}
