<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ProductSaleFactory extends Factory
{
    public function definition()
    {
        $start = Carbon::now();

        return [
            'name' => $this->faker->sentence(2),
            'product_id' => 1,
            'price_sale' => $this->faker->randomFloat(2, 50, 500),
            'date_begin' => $start,
            'date_end' => $start->copy()->addDays(7),
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ];
    }
}
