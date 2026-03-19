<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(2),
            'link' => '/',
            'type' => $this->faker->randomElement(['category', 'page', 'topic', 'custom']),
            'parent_id' => 0,
            'sort_order' => 0,
            'table_id' => null,
            'position' => $this->faker->randomElement(['mainmenu', 'footermenu']),
            'created_at' => now(),
            'created_by' => 1,
            'status' => 1,
        ];
    }
}
