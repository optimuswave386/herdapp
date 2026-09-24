<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Optional demo data so the dashboard has something to show:
 *
 *     php artisan db:seed --class=DemoOrdersSeeder
 *
 * Creates ~60 orders spread over the last ~6 months from your existing users and
 * active products (a few are created if you have none). It does not touch stock.
 */
class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->limit(15)->get();
        if ($users->count() < 3) {
            $users = User::factory()->count(5)->create();
        }

        $products = Product::where('status', 'active')->get();
        if ($products->isEmpty()) {
            $products = Product::factory()->count(8)->create(['status' => 'active', 'stock_quantity' => 50]);
        }

        // Mostly paid/completed, a few pending and cancelled, like a real shop.
        $statuses = array_merge(
            array_fill(0, 45, Order::PAID), array_fill(0, 15, Order::SHIPPED), array_fill(0, 20, Order::COMPLETED),
            array_fill(0, 12, Order::PENDING), array_fill(0, 8, Order::CANCELLED),
        );

        for ($i = 0; $i < 60; $i++) {
            $placed = now()->subDays(random_int(0, 170))->subMinutes(random_int(0, 1440));
            $user = $users->random();

            $order = Order::create([
                'number' => 'TMP-'.bin2hex(random_bytes(6)),
                'user_id' => $user->id,
                'status' => $statuses[array_rand($statuses)],
                'total' => 0,
                'payment_method' => array_rand(Order::PAYMENT_METHODS),
                'shipping_name' => $user->name,
                'shipping_address' => fake()->streetAddress(),
                'shipping_city' => fake()->city(),
                'shipping_postal_code' => fake()->postcode(),
                'shipping_country' => 'United States',
            ]);

            $total = 0;
            foreach ($products->random(min(random_int(1, 4), $products->count())) as $product) {
                $quantity = random_int(1, 3);
                $line = round((float) $product->price * $quantity, 2);
                $total += $line;
                $order->items()->create([
                    'product_id' => $product->id, 'product_name' => $product->name,
                    'unit_price' => $product->price, 'quantity' => $quantity, 'line_total' => $line,
                ]);
            }

            $order->forceFill([
                'number' => 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'total' => round($total, 2),
                'created_at' => $placed,
                'updated_at' => $placed,
            ])->save();
        }
    }
}
