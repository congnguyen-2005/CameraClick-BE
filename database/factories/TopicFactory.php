<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TopicFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'slug' => Str::slug($this->faker->word()),
            'sort_order' => 0,
            'description' => $this->faker->sentence(8),
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ];
    }
}
