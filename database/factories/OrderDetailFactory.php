<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    public function definition()
    {
        return [
            'order_id' => 1,
            'product_id' => 1,
            'price' => $this->faker->randomFloat(2, 100, 500),
            'qty' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 100, 500),
            'discount' => $this->faker->randomFloat(2, 0, 50),
        ];
    }
}
