<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Coupon extends Model
{
    /**
     * The default rule group.
     */
    const RULE_GROUP_DEFAULT = 'default';

    /**
     * Return the carts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * @param \App\Models\CouponRule $rule
     * @return \App\Models\Coupon
     * @throws \Throwable
     */
    public function addDiscountRule(CouponRule $rule)
    {
        if ($rule->rule_type !== CouponRule::RULE_DISCOUNT) {
            throw new InvalidArgumentException(__FUNCTION__ . " expects a '" . CouponRule::RULE_DISCOUNT . "' rule.");
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
                    throw new Exception("Failed to add coupon rule.");
                }
            }
        });

        return $this;
    }

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
     * @param \App\Models\CouponRule $rule
     * @return \App\Models\Coupon
     * @throws \Throwable
     */
    public function addCouponRule(CouponRule $rule)
    {
        if ($rule->rule_type !== CouponRule::RULE_COUPON) {
            throw new InvalidArgumentException(__FUNCTION__ . " expects a '" . CouponRule::RULE_COUPON . "'' rule.");
        }

        return $this->addRules($rule);
    }

    /**
     * Build and get the coupon rule expression.
     *
     * @return string
     */
    public function getCouponRuleExpressionAttribute()
    {
        $rules = $this->couponRules()->orderBy('rule_order')->orderBy('id')->get();

        $expression = "";
        $nextJoin   = "";

        foreach ($rules as $index => $rule) {
            if ($nextJoin !== "") {
                $expression .= $nextJoin;
            }

            $nextJoin   = sprintf(" %s ", $rule->rule_operator ?? 'and');
            $expression .= $rule->rule_not ? 'not ' : '';
            $expression .= $rule->rule;
        }

        return $expression;
    }

    /**
     * Build and get the coupon rule expression.
     *
     * @return string
     */
    public function getCouponDiscountExpressionAttribute()
    {
        $rules = $this->discountRules()->orderBy('rule_order')->orderBy('id')->get();

        $expression = "";
        $nextJoin   = "";

        foreach ($rules as $index => $rule) {
            if ($nextJoin !== "") {
                $expression .= $nextJoin;
            }

            $nextJoin   = sprintf(" %s ", $rule->rule_operator ?? 'and');
            $expression .= $rule->rule_not ? 'not ' : '';
            $expression .= $rule->rule;
        }

        return $expression;
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
}
