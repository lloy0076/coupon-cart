<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CouponRule extends Model
{
    /**
     * A coupon rule.
     */
    const RULE_COUPON = 'coupon';

    /**
     * A discount rule.
     */
    const RULE_DISCOUNT = 'discount';

    /**
     * Another type of rule.
     */
    const RULE_OTHER = 'other';

    /**
     * Makes a new rule.
     *
     * @param array $data
     * @return \App\Models\CouponRule
     */
    public static function makeNewRule($data = [])
    {
        $defaults = [
            'coupon_id'     => 0,
            'rule'          => 'true == true', // Always return true.
            'rule_type'     => CouponRule::RULE_COUPON,
            'description'   => null,
            'rule_order'    => 1,
            'rule_not'      => false,
            'rule_operator' => 'and',
        ];

        $merged = array_merge($defaults, $data);

        $rule = new CouponRule();

        foreach ($merged as $index => $value) {
            $rule->$index = $value;
        }

        return $rule;
    }

    /**
     * Makes a new coupon rule.
     *
     * @param array $data
     * @return \App\Models\CouponRule
     */
    public static function makeNewCouponRule($data = []) {
        $data['rule_type'] = CouponRule::RULE_COUPON;
        return static::makeNewRule($data);
    }

    /**
     * Makes a new discount rule.
     *
     * @param array $data
     * @return \App\Models\CouponRule
     */
    public static function makeNewDiscountRule($data = []) {
        $data['rule_type'] = CouponRule::RULE_DISCOUNT;
        return static::makeNewRule($data);
    }

    /**
     * The associated coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Only the coupon rules.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCoupon(Builder $query)
    {
        return $query->where('rule_type', CouponRule::RULE_COUPON);
    }

    /**
     * Only the discount rules.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiscount(Builder $query)
    {
        return $query->where('rule_type', CouponRule::RULE_DISCOUNT);
    }
}
