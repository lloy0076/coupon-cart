<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use DatabaseMigrations;

    public function testProduct()
    {
        $this->seed();

        $products = new Product();
        $this->assertIsObject($products, "It is a product");

        $allProducts = $products->get();
        $this->assertIsNotNumeric($products, "There are some products");

        // Verify each found product.
        foreach ($allProducts as $index => $product) {
            $name = $product->name;
            list ($expectedPrice, $rest) = explode(' ', $name);

            $this->assertEquals("Item", $rest, "It should be the word 'Item'");

            $this->assertEquals($expectedPrice,
                $product->price,
                "The expected price $expectedPrice should be " . $product->price);

            $this->assertNotFalse(filter_var($product->image, FILTER_VALIDATE_URL),
                "The image should be a URL: " . $product->image);
        }
    }
}
