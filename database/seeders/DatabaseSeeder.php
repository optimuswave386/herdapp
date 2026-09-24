<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // You can uncomment the following lines to call multiple seeders
        // $this->call([
        //     RolesAndPermissionsSeeder::class,
        //     UserSeeder::class,
        // ]);
        
        $this->call(RolesAndPermissionsSeeder::class);

    }
}
