<?php

use App\Models\Cart;
use App\Models\Product;
use App\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /*
     * We'll create two carts:
     *
     * 1. One with two items;
     * 2. One with no items.
     *
     * @throws \Exception
     */
    public function run()
    {
        $user = User::orderBy('id')->first();

        if (true || !isset($user)) {
            $user = factory(User::class)->create();
        }

        $products = Product::where('price', '>', 0)->get();

        if (count($products) > 2) {
            $p1 = $products[0];
            $p2 = $products[1];
            $p3 = $products[2];
        } else {
            throw new Exception('Not enough products.');
        }

        $this->makeCartWithTwoItems($user, $p1, $p2);
        $this->makeCartWithOneItem($user, $p3);
        $this->make1001DollarCartWithFourItems($user);
        $this->makeCartWithSingleExpensiveItem($user);
    }

    /**
     * @param $user
     * @param $p1
     * @param $p2
     */
    protected function makeCartWithTwoItems($user, $p1, $p2): void
    {
        $cart = factory(Cart::class)->create([
            'user_id'      => $user->id,
            'instructions' => 'Cart with two items',
        ]);

        $cart->addItem($p1);
        $cart->addItem($p2);

        $user->carts()->save($cart);
    }

    /**
     * @param $user
     * @param $p3
     * @return mixed
     */
    protected function makeCartWithOneItem($user, $p3): void
    {
        $cart = factory(Cart::class)->create([
            'user_id'      => $user->id,
            'instructions' => 'Cart with one item',
        ]);

        $cart->addItem($p3);

        $user->carts()->save($cart);
    }

    /**
     * @param $user
     * @throws \Exception
     */
    protected function make1001DollarCartWithFourItems($user): void
    {
        $product = Product::where('price', (float)1001)->first();

        if (!isset($product)) {
            throw new Exception("A product with price of 1001 is needed.");
        }

        $cart = factory(Cart::class)->create([
            'user_id'      => $user->id,
            'instructions' => 'Cart with expensive item',
        ]);

        $zero = Product::where('price', 0)->first();

        if (!isset($zero)) {
            throw new Exception("A product with price of 0 is needed.");
        }

        $cart->addItem($product);
        // We need to have a few items but I don't want to bump the price too high.
        $cart->addItem($zero, 3);

        $user->carts()->save($cart);
    }

    /**
     * @param $user
     * @throws \Exception
     */
    protected function makeCartWithSingleExpensiveItem($user): void
    {
        $product = Product::where('price', (float)1001)->first();

        if (!isset($product)) {
            throw new Exception("A product with price of 1001 is needed.");
        }
        $cart = factory(Cart::class)->create([
            'user_id'      => $user->id,
            'instructions' => 'Cart with single expensive item',
        ]);

        $cart->addItem($product);

        $user->carts()->save($cart);
    }
}
