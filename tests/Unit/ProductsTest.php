<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use DatabaseMigrations;

    private $setup = true;

    /**
     * Test Products
     *
     * 1. We should be able to get a product.
     * 2. We should be able to get them.
     * 3. The number in the description, multiplied by 100, should be the price retrieved from the model.
     *
     * @return void
     */
    public function testProduct()
    {
        $this->seed();

        $products = new Product();
        $this->assertIsObject($products, "It is a product");

        $allProducts = $products->get();
        $this->assertIsNotNumeric($products, "There are some products");

        foreach ($allProducts as $index => $product) {
            $name = $product->name;
            list ($expectedPrice, $rest) = explode(' ', $name);

            $this->assertEquals("Item", $rest, "It should be the word 'Item'");
            $this->assertEquals($expectedPrice * 100,
                $product->price,
                "The expected price $expectedPrice should be " . $product->price);

            $this->assertNull($product->tax_percent, "Tax is not implemented and should be null.");
            $this->assertNull($product->tax_description, "Tax is not implemented and should be null.");
        }
    }
}
