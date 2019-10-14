<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Gets the price in cents.
     *
     * @return float|int
     */
    public function getPriceAttribute()
    {
        return $this->attributes['price'] * 100;
    }

    /**
     * Gets the tax per cent divided by 100 (usually between 0 and 1) _or_ returns null.
     *
     * @return float|int|null
     */
    public function getTaxPercentAttribute()
    {
        if (isset($this->attributes['tax_percent'])) {
            return $this->attributes['tax_percent'] / 100;
        } else {
            return null;
        }
    }
}
