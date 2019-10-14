<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $data = [
            49.45,
            49.99,
            49,
            50,
            50.01,
            51,
            66.66,
            66.67,
            67,
            99,
            99.99,
            100,
            100.01,
            100,
            999,
            999.99,
            1000,
            1000.01,
            1001,
        ];

        foreach ($data as $index => $value) {
            factory(Product::class)->create([
                'name' => "$value Item",
                'price' => $value,
            ]);
        }
    }
}
