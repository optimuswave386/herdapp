<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the Posts seeds.
     */
    public function run(): void
    {

        //  User::factory()->create(
        //     [
        //         'name' => 'user1',
        //         'email' => 'user1@user1',
        //         'password' => bcrypt('password'),  
        //     ]
        //  );
        
        User::factory(10)->create();
        //User::factory()->count(50)->create();
            
    }
}