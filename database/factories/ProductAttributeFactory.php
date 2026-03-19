<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    public function definition()
    {
        return [
            'product_id' => 1,
            'attribute_id' => 1,
            'value' => $this->faker->word(),
        ];
    }
}
