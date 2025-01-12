<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "description" => $this->faker->text,
            "start_at" => $this->faker->date(),
            "project_id" => Project::factory(),
        ];
    }
}
