<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    /**
     * The default rule group.
     */
    const RULE_GROUP_DEFAULT = 'default';

    /**
     * The coupon rules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function couponRules()
    {
        return $this->hasMany(CouponRule::class)->coupon();
    }

    /**
     * The coupon rules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discountRules()
    {
        return $this->hasMany(CouponRule::class)->discount();
    }

    /**
     * @param \App\Models\CouponRule $rule
     * @return \App\Models\Coupon
     * @throws \Throwable
     */
    public function addDiscountRule(CouponRule $rule) {
        if ($rule->rule_type !== CouponRule::RULE_DISCOUNT) {
            throw new \InvalidArgumentException(__FUNCTION__ . " expects a '" . CouponRule::RULE_DISCOUNT. "' rule.");
        }

        return $this->addRules($rule);
    }

    /**
     * @param \App\Models\CouponRule $rule
     * @return \App\Models\Coupon
     * @throws \Throwable
     */
    public function addCouponRule(CouponRule $rule) {
        if ($rule->rule_type !== CouponRule::RULE_COUPON) {
            throw new \InvalidArgumentException(__FUNCTION__ . " expects a '" . CouponRule::RULE_COUPON . "'' rule.");
        }

        return $this->addRules($rule);
    }

    /**
     * Adds one or more rules; rules may be of any type.
     *
     * @param array $rules If an array of arrays, each array is a product object/quantity pair; else the first
     *     argument should be a \App\Models\Product.
     * @return \App\Models\Coupon
     * @throws \Throwable
     */
    public function addRules($rules = [])
    {
        if (is_object($rules) && $rules instanceof CouponRule) {
            $rules = [$rules];
        }

        DB::transaction(function () use ($rules) {
            foreach ($rules as $rule) {
                if (is_array($rule)) {
                    $item = CouponRule::makeNewRule($rule);
                } else {
                    $item = $rule;
                }

                $didSave = $this->couponRules()->save($item);

                if (!$didSave) {
                    throw new \Exception("Failed to add coupon rule.");
                }
            }
        });

        return $this;
    }
}
