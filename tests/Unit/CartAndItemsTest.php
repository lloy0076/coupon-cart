<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Product;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CartAndItemsTest extends TestCase
{
    use DatabaseMigrations;

    public function testDoubleCartAndItems()
    {
        $this->seed();

        $theCart = Cart::where("instructions", "Cart with two items")->first();

        $this->assertNotNull($theCart, "Cart with two items exists");
        $this->assertEquals(2, $theCart->numberOfCartItems(), "There are two items");
        $this->assertEquals(99.44, (float)$theCart->total_inc, "The total inc should be 99.44");
    }

    public function testSingleCartAndItems()
    {
        $this->seed();

        $theCart = Cart::where("instructions", "Cart with one item")->first();

        $this->assertNotNull($theCart, "Cart with two items exists");
        $this->assertEquals(1, $theCart->numberOfCartItems(), "There is only one item");
        $this->assertEquals(49.00, (float)$theCart->total_inc, "The total inc should be 49.00");
    }

    /**
     * This also tests numberOfCartItems().
     */
    public function testExpensiveCartAndItems()
    {
        $this->seed();

        $theCart = Cart::where("instructions", "Cart with expensive item")->first();

        $this->assertNotNull($theCart, "Cart with expensive items exists");
        $this->assertEquals(4, $theCart->numberOfCartItems(), "There should be four items");
        $this->assertGreaterThan((float)1000.00,
            (float)$theCart->total_inc,
            "The total inc should be greater than 1000.00");
    }

    /**
     * This also tests numberOfCartItems().
     */
    public function testSingleItemExpensiveCartAndItems()
    {
        $this->seed();

        $theCart = Cart::where("instructions", "Cart with single expensive item")->first();

        $this->assertNotNull($theCart, "Cart with expensive items exists");
        $this->assertEquals(1, $theCart->numberOfCartItems(), "There should be four items");
        $this->assertGreaterThan((float)1000.00,
            (float)$theCart->total_inc,
            "The total inc should be greater than 1000.00");
    }

    public function testRemoveFromSingleCartAndItems()
    {
        $this->seed();

        $user = User::orderBy('id')->first();

        if (true || !isset($user)) {
            $user = factory(User::class)->create();
        }

        $cart = factory(Cart::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($cart instanceof Cart, "It should be a cart");

        $product = Product::orderBy('id')->first();
        $cart->addItem($product, 1);

        $priceInc = $product->price_inc;

        $this->assertEquals((float)$product->price_inc, (float)$cart->total_inc, "The total should be $priceInc");

        $lineItem = $cart->cartItems->first();
        $cart->removeItem($lineItem);

        $this->assertEquals((float)0, (float)$cart->total_inc, "The total should be 0.00");
    }
}
