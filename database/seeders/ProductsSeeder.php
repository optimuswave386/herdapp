<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the Products seeds.
     */
    public function run(): void
    {
        //Product::factory()->count(50)->create();    
        Product::factory(10)->create();            
    }
}