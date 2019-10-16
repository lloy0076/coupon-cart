<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tests\TestCase;

/**
 * Class CouponRuleAndDiscountsTest
 *
 * This set of tests checks the calculations; it assumes the setup is mostly sane.
 *
 * @package Tests\Unit
 */
class CouponCalculationsTest extends TestCase
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

    public function testFixed10Applies()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 1001)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'FIXED10');
        $this->assertTrue($coupon->exists(), "The FIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(991, $cart->total_inc, "Initial total inc should be 991");
    }

    public function testFixed10LowAmount()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 49)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'FIXED10');
        $this->assertTrue($coupon->exists(), "The FIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(49, $cart->total_inc, "Initial total inc should be 49");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(49, $cart->total_inc, "Initial total inc should be 49");
    }

    public function testFixed10HighAmountOneItem()
    {
        $this->seed();

        $cart = Cart::where("instructions", "Cart with single expensive item")->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'FIXED10');
        $this->assertTrue($coupon->exists(), "The FIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(991, $cart->total_inc, "Initial total inc should be 1001");
    }

    public function testPercent10()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 1001)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'PERCENT10');
        $this->assertTrue($coupon->exists(), "The PERCENT10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(900.90, $cart->total_inc, "Initial total inc should be 900.90");
    }

    public function testPercent10LowAmount()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 49)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'PERCENT10');
        $this->assertTrue($coupon->exists(), "The PERCENT10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(49.00, $cart->total_inc, "Initial total inc should be 49");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(49.00, $cart->total_inc, "Initial total inc should be 49");
    }

    public function testPercent10HighAmountOneItem()
    {
        $this->seed();

        $cart = Cart::where("instructions", "Cart with single expensive item")->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'PERCENT10');
        $this->assertTrue($coupon->exists(), "The PERCENT10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001.00, $cart->total_inc, "Initial total inc should be 1001.00");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(1001.00, $cart->total_inc, "Initial total inc should be 1001.00");
    }

    public function testMixed10HighValue()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 1001)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'MIXED10');

        $this->assertTrue($coupon->exists(), "The MIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001.00, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(900.90, $cart->total_inc, "Initial total inc should be 900.90");
    }

    public function testMixed10LowValue()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 49)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $zero = Product::where('price', '=', 0)->first();
        $this->assertInstanceOf(Product::class, $zero, "There should be a zero dollar product");

        // Force a number of items on so we don't fail the low item part.
        if ($cart->numberOfCartItems() < 3) {
            $cart = $cart->addItem($zero, 3);
        }

        $this->assertGreaterThan(2, $cart->numberOfCartItems(), "There should be at least 2 items");

        $coupon = Coupon::where('coupon_code', 'MIXED10');

        $this->assertTrue($coupon->exists(), "The MIXED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(49.00, $cart->total_inc, "Initial total inc should be 49.00");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(49, $cart->total_inc, "Initial total inc should be 49.00");
    }

    public function testMixed10HighAmountOneItem()
    {
        $this->seed();

        $cart = Cart::where("instructions", "Cart with single expensive item")->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'PERCENT10');
        $this->assertTrue($coupon->exists(), "The PERCENT10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");
    }

    public function testRejected10()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 1001)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'REJECTED10');
        $this->assertTrue($coupon->exists(), "The REJECTED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(1001, $cart->total_inc, "Initial total inc should be 1001");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        // Take $10 off and then take 10% off that amount.
        $expected = (1001 - 10) - ((1001 - 10) * 10/100);
        $this->assertEquals($expected, $cart->total_inc, "Initial total inc should be 891.9");
    }

    public function testRejected10LowAmount()
    {
        $this->seed();

        $cart = Cart::where('total_inc', 99.44)->first();
        $this->assertInstanceOf(Cart::class, $cart, "There should be at least one cart");

        $coupon = Coupon::where('coupon_code', 'REJECTED10');
        $this->assertTrue($coupon->exists(), "The REJECTED10 coupon should exist");

        $coupon = $coupon->first();

        $this->assertEquals(99.44, $cart->total_inc, "Initial total inc should be 99.44");

        $result = $cart->applyCoupon($coupon);
        $this->assertInstanceOf(Cart::class,  $result, "It should be a cart");

        $this->assertEquals(99.44, $cart->total_inc, "Initial total inc should be 99.44");
    }
}
