<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStoreFactory extends Factory
{
    public function definition()
    {
        return [
            'product_id' => 1,
            'price_root' => $this->faker->randomFloat(2, 100, 500),
            'qty' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ];
    }
}
