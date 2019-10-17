<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /**
     * Makes a new item.
     *
     * @param \App\Models\Product $product
     * @param int                 $quantity
     * @return \App\Models\CartItem
     */
    public static function makeNewCartItem(Product $product, $quantity = 1) {
        $item = new CartItem();

        $item->product_id = $product->id;
        $item->quantity = $quantity;
        $item->price_inc = $product->price_inc * $quantity;

        return $item;
    }

    /**
     * The associated cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * The associated product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
