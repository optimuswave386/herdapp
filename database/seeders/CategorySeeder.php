<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        // Category::factory()->create([
        //     'name' => 'Technology',
        //     'description' => 'Posts related to technology.',
        // ]);

        $categoryNames = ['Food', 'Lifestyle', 'Education', 'Finance', 'Entertainment', 'Sports', 'Science'];
        foreach ($categoryNames as $name) {
            Category::factory()->create([
                'name' => $name,
                'description' => "Posts related to {$name}.",
            ]);
        }

    }
}
