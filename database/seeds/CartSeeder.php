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
     */
    public function run()
    {
        $user = User::orderBy('id')->first();

        if (true || !isset($user)) {
            $user = factory(User::class)->create();
        }

        $products = Product::get();

        if (count($products) > 2) {
            $p1 = $products[0];
            $p2 = $products[1];
            $p3 = $products[2];
        } else {
            throw new \Exception('Not enough products.');
        }

        $cart = factory(Cart::class)->create([
            'instructions' => 'Cart with two items',
        ]);

        $cart->addItem($p1);
        $cart->addItem($p2);

        $user->carts()->save($cart);

        $cart = factory(Cart::class)->create([
            'instructions' => 'Cart with one item',
        ]);

        $cart->addItem($p3);

        $user->carts()->save($cart);
    }
}
