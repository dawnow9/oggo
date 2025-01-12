<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "description" => $this->faker->text,
            "start_at" => $this->faker->date(),
        ];
    }
}
