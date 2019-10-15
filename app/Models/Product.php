<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Get the tax inclusive price.
     *
     * @return float|int
     */
    public function getPriceIncAttribute()
    {
        $taxPercent = $this->tax_percent;

        if (isset($taxPercent) && $taxPercent > 0) {
            return $this->price * (1 + $taxPercent / 100);
        }

        return $this->price;
    }
}
