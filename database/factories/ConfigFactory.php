<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigFactory extends Factory
{
    public function definition()
    {
        return [
            'site_name' => $this->faker->company(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'hotline' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'status' => 1,
        ];
    }
}
