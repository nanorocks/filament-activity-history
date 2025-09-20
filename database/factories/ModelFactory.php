<?php

namespace Nanorocks\FilamentActivityHistory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nanorocks\FilamentActivityHistory\Models\Activity;

class ModelFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        return [
            'log_name' => $this->faker->optional()->word,
            'description' => $this->faker->sentence,
            'subject_id' => $this->faker->optional()->randomNumber(),
            'subject_type' => $this->faker->optional()->word,
            'causer_id' => $this->faker->optional()->randomNumber(),
            'causer_type' => $this->faker->optional()->word,
            'properties' => $this->faker->optional()->jsonEncode(['key' => $this->faker->word]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
