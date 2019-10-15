<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    /**
     * The cart items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->cartItems();
    }

    /**
     * The cart items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * The user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate sum of the items.
     */
    public function totalCartCost()
    {
        return $this->cartItems->sum->price_inc;
    }

    /**
     * Return the number of cart items.
     *
     * @return int
     */
    public function numberOfCartItems()
    {
        return count($this->cartItems);
    }

    /**
     * Adds a single item.
     *
     * @param \App\Models\Product $product
     * @param int                 $quantity
     * @return \App\Models\Cart
     * @throws \Throwable
     */
    public function addItem(Product $product, $quantity = 1)
    {
        $this->addItems($product, $quantity);

        return $this;
    }

    /**
     * Adds a list of items.
     *
     * @param array $products If an array of arrays, each array is a product object/quantity pair; else the first
     *     argument should be a \App\Models\Product.
     * @param int   $quantity
     * @return \App\Models\Cart
     * @throws \Throwable
     */
    public function addItems($products = [], $quantity = 1)
    {
        if (is_object($products) && $products instanceof Product) {
            $products = [[$products, $quantity]];
        }

        DB::transaction(function () use ($products) {
            foreach ($products as $product) {
                $item    = CartItem::makeNewCartItem($product[0], $product[1] ?? 1);
                $didSave = $this->cartItems()->save($item);

                if (!$didSave) {
                    throw new \Exception("Failed to add item with id " . $item->id);
                }
            }

            $this->recalculateTotals();
        });

        return $this;
    }

    /**
     * Recalculates the totals.
     *
     * @return $this
     * @throws \Exception
     */
    protected function recalculateTotals()
    {
        $this->total_inc = $this->cartItems()->get()->sum->price_inc;

        $didSave = $this->save();

        if (!$didSave) {
            throw new \Exception("Failed to update cart with id " . $this->id);
        }

        return $this;
    }

    /**
     * Removes the given item.
     *
     * @param \App\Models\CartItem $cartItem
     * @return \App\Models\Cart
     * @throws \Throwable
     */
    public function removeItem(CartItem $cartItem)
    {
        return $this->removeItems($cartItem);
    }

    /**
     * Removes the given items.
     *
     * @param array $cartItems An array or a single item.
     * @return \App\Models\Cart
     * @throws \Throwable
     */
    public function removeItems($cartItems = [])
    {
        if (is_object($cartItems) && $cartItems instanceof CartItem) {
            $cartItems = [$cartItems];
        }

        DB::transaction(function () use ($cartItems) {
            foreach ($cartItems as $cartItem) {
                $didDelete = $cartItem->delete();

                if (!$didDelete) {
                    throw new \Exception("Unable to delete cart item id " . $cartItem);
                }
            }

            $this->recalculateTotals();
        });

        return $this;
    }
}
