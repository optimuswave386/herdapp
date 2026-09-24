<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PhotosFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photoid' => fake()->unique()->numberBetween(1, 10000),
            'userid' => fake()->numberBetween(1, 1000),
            'phototitle' => fake()->phototitle(),
            'photodescription' => fake()->photodescription(),
            'photoblob' => Str::random(10),
            'photolocation' => fake()->photolocation(),
            'dateuploaded' => now(),
            'datemodified' => now(),
        ];
    }

}