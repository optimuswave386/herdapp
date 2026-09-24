<?php

namespace Database\Seeders;

use App\Models\Posts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    /**
     * Run the Posts seeds.
     */
    public function run(): void
    {

        // Posts::factory()->create(
        //     [
        //         'title' => 'First Post',
        //         'content' => 'This is the content of the first post.',
        //         'author' => 'John Doe',
        //         'excerpt' => 'This is the excerpt of the first post.',
        //         'image' => '../public/images/image1.jpg',
        //         'slug' => 'first-post',
        //         'category_id' => 1,
        //         'user_id' => 1,
        //     ]
        // );
    
        Posts::factory(10)->create();
        //Posts::factory()->count(50)->create();
            
    }
}