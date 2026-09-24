<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Posts>
 */
class PostsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 'id' => $this->faker->unique()->numberBetween(1, 1000),
        // 'created_at' => $this->faker->dateTime(),
        // 'updated_at' => $this->faker->dateTime(),

        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'author' => $this->faker->name(),
            'excerpt' => $this->faker->text(100),
            'image' => $this->faker->imageUrl(),
            'slug' => $this->faker->unique()->slug(),
            'category_id' => $this->faker->numberBetween(1, 5),
            'user_id' => $this->faker->numberBetween(1, 5),
        ];
    }
}
