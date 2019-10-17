<?php

namespace App\Models;

use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use jkIlluminate\Support\Facades\Log;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
     * Return the number of cart items.
     *
     * @return int
     */
    public function numberOfCartItems()
    {
        // Force an update.
        $this->refresh();

        $numberOfItems = $this->cartItems->pluck('quantity')->sum();

        return $numberOfItems;
    }

    /**
     * Get the associated coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
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
                    throw new Exception("Failed to add item with id " . $item->id);
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
        $discount        = $this->calculateDiscountGivenCoupon();
        $this->total_inc = $this->cartItems()->get()->sum->price_inc - $discount;

        $didSave = $this->save();

        if (!$didSave) {
            throw new Exception("Failed to update cart with id " . $this->id);
        }

        return $this;
    }

    /**
     * Set the coupon (if passed in) and get the discount.
     *
     * @param \App\Models\Coupon $coupon
     * @return float|int
     * @throws \Exception
     */
    public function calculateDiscountGivenCoupon(Coupon $coupon = null)
    {
        if (!isset($coupon) || is_null($coupon)) {
            $coupon = $this->coupon;
        }

        if (!isset($coupon) || is_null($coupon)) {
            // If there is no coupon, there's no discount!
            return 0;
        }

        $interpreter = new ExpressionLanguage();

        $couponRulesExpression = $coupon->coupon_rule_expression;

        $grossCost         = $this->grossCartCost();
        $originalGrossCost = $grossCost;
        $discount          = 0;

        if ($interpreter->evaluate($couponRulesExpression, ['cart' => $this])) {
            $discountRulesExpression = $coupon->coupon_discount_expression;
            $discountRules           = $interpreter->evaluate($discountRulesExpression, ['cart' => $this]);

            $rules = explode(';', $discountRules);

            foreach ($rules as $index => $rule) {
                if ($grossCost <= 0) {
                    break;
                }

                if (preg_match('/^\$(\d+(?:\.\d+)?)$/', $rule, $matches)) {
                    $reduceBy  = floatval($matches[1]);
                    $grossCost -= $reduceBy;
                    $discount  += $reduceBy;
                    continue;
                }

                if (preg_match('/^%(\d+(?:\.\d+)?)$/', $rule, $matches)) {
                    $reduceBy  = floatval($matches[1]) / 100 * $grossCost;
                    $grossCost -= $reduceBy;
                    $discount  += $reduceBy;

                    continue;
                }
            }
        }

        // We really should do something better than this; but this works.
        if ($grossCost < 0) {
            $discount = $originalGrossCost;
        }

        return $discount;
    }

    /**
     * Calculate sum of the items.
     */
    public function grossCartCost()
    {
        return $this->cartItems->sum->price_inc;
    }

    /**
     * Apply the given coupon.
     *
     * @param \App\Models\Coupon $coupon
     * @return \App\Models\Cart
     * @throws \Throwable
     */
    public function applyCoupon(Coupon $coupon)
    {
        return DB::transaction(function () use ($coupon) {
            $coupon->carts()->save($this);

            return $this->recalculateTotals();
        });
    }

    /**
     * Remove any coupons.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function removeCoupons()
    {
        return DB::transaction(function () {
            $this->coupon_id = null;

            $didSave = $this->save();

            if (!$didSave) {
                throw new Exception("Failed to save whilst removing coupon");
            }

            $this->refresh();

            return $this->recalculateTotals();
        });
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
                    throw new Exception("Unable to delete cart item id " . $cartItem);
                }
            }

            $this->recalculateTotals();
        });

        return $this;
    }
}
