<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductImageFactory extends Factory
{
    public function definition()
    {
        return [
            'product_id' => 1,
            'image' => 'uploads/product/' . Str::random(10) . '.jpg',
            'alt' => $this->faker->word(),
            'title' => $this->faker->word(),
        ];
    }
}
