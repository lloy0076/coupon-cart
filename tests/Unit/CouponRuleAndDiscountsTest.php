<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Coupon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tests\TestCase;

/**
 * Class CouponRuleAndDiscountsTest
 *
 * This set of tests parses for syntax - it does not check if the coupon processor calculates totals properly.
 *
 * @package Tests\Unit
 */
class CouponRuleAndDiscountsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * The expression language object.
     *
     * @var
     */
    protected $interpreter;

    public function setUp(): void
    {
        parent::setUp();

        $this->interpreter = new ExpressionLanguage();
    }

    public function testFixed10()
    {
        $this->seed();

        $cart = Cart::first();
        $this->assertNotNull($cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'FIXED10');
        $this->assertTrue($coupon->exists(), "The FIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals("Fixed 10", $coupon->display_name);

        $couponExpression = $coupon->coupon_rule_expression;
        $this->assertEquals("cart.grossCartCost() > 50 and cart.numberOfCartItems() > 0", $couponExpression);

        try {
            $result = $this->interpreter->evaluate($couponExpression, ['cart' => $cart]);
            $this->assertIsBool($result, "The result should be a boolean");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $discountExpression = $coupon->coupon_discount_expression;
        $this->assertEquals('"$10"', $discountExpression);

        $result = null;

        try {
            $result = $this->interpreter->evaluate($discountExpression, ['cart' => $cart]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals('$10', $result, "Result $result");
    }

    public function testPercent10()
    {
        $this->seed();

        $cart = Cart::first();
        $this->assertNotNull($cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'PERCENT10');
        $this->assertTrue($coupon->exists(), "The PERCENT10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals("Percent 10", $coupon->display_name);

        $couponExpression = $coupon->coupon_rule_expression;
        $this->assertEquals('cart.numberOfCartItems() > 1 and cart.grossCartCost() > 100', $couponExpression);

        try {
            $result = $this->interpreter->evaluate($couponExpression, ['cart' => $cart]);
            $this->assertIsBool($result, "The result should be a boolean");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $discountExpression = $coupon->coupon_discount_expression;
        $this->assertEquals('"%10"', $discountExpression);

        $result = null;

        try {
            $result = $this->interpreter->evaluate($discountExpression, ['cart' => $cart]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals('%10', $result);
    }

    public function testMixed10()
    {
        $this->seed();

        $cart = Cart::first();
        $this->assertNotNull($cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'MIXED10');
        $this->assertTrue($coupon->exists(), "The MIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals("Mixed 10", $coupon->display_name);

        $couponExpression = $coupon->coupon_rule_expression;
        $this->assertEquals('cart.grossCartCost() > 200 and cart.numberOfCartItems() > 2', $couponExpression);

        try {
            $result = $this->interpreter->evaluate($couponExpression, ['cart' => $cart]);
            $this->assertIsBool($result, "The result should be a boolean");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $discountExpression = $coupon->coupon_discount_expression;
        $this->assertEquals('cart.grossCartCost() * 0.1 > 10 ? "%10" : "$10"', $discountExpression);

        $result = null;

        try {
            $result = $this->interpreter->evaluate($discountExpression, ['cart' => $cart]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals('$10', $result);
    }

    public function testRejected10()
    {
        $this->seed();

        $cart = Cart::first();
        $this->assertNotNull($cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'REJECTED10');
        $this->assertTrue($coupon->exists(), "The REJECTED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals("Rejected 10", $coupon->display_name);

        $couponExpression = $coupon->coupon_rule_expression;
        $this->assertEquals('cart.grossCartCost() > 1000', $couponExpression);

        try {
            $result = $this->interpreter->evaluate($couponExpression, ['cart' => $cart]);
            $this->assertIsBool($result, "The result should be a boolean");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $discountExpression = $coupon->coupon_discount_expression;
        $this->assertEquals('"$10;%10"', $discountExpression);

        $result = null;

        try {
            $result = $this->interpreter->evaluate($discountExpression, ['cart' => $cart]);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals('$10;%10', $result);
    }
}
