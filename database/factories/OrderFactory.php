<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'number' => 'ORD-'.strtoupper(fake()->unique()->bothify('??####')),
            'user_id' => User::factory(),
            'status' => Order::PAID,
            'total' => fake()->randomFloat(2, 10, 400),
            'payment_method' => fake()->randomElement(array_keys(Order::PAYMENT_METHODS)),
            'shipping_name' => fake()->name(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_country' => 'United States',
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
