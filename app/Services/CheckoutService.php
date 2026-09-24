<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Turn a cart into an order.
     *
     * The cart only says WHICH products and HOW MANY. Names and prices are read from the
     * database inside the transaction, so a tampered browser cart cannot change what is charged.
     * Stock is checked and taken under a row lock so two shoppers cannot buy the last unit.
     *
     * @param  array<int, array{product_id: int|string, quantity: int|string}>  $items
     * @param  array<string, mixed>  $details  shipping_* fields, payment_method, notes
     *
     * @throws ValidationException when something in the cart can't be fulfilled
     */
    public function placeOrder(User $user, array $details, array $items): Order
    {
        // The same product may appear twice; treat it as one line.
        $wanted = [];
        foreach ($items as $item) {
            $id = (int) $item['product_id'];
            $wanted[$id] = ($wanted[$id] ?? 0) + (int) $item['quantity'];
        }

        return DB::transaction(function () use ($user, $details, $wanted) {
            $products = Product::whereIn('id', array_keys($wanted))->lockForUpdate()->get()->keyBy('id');

            $problems = [];
            $lines = [];
            $total = 0;

            foreach ($wanted as $id => $quantity) {
                $product = $products->get($id);

                if (! $product || ! $product->isActive()) {
                    $problems[] = ($product?->name ?? 'A product in your cart').' is no longer available.';
                    continue;
                }
                if ($product->stock_quantity < $quantity) {
                    $problems[] = $product->stock_quantity > 0
                        ? "Only {$product->stock_quantity} of {$product->name} left in stock."
                        : "{$product->name} is out of stock.";
                    continue;
                }

                $lineTotal = round((float) $product->price * $quantity, 2);
                $total += $lineTotal;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ];
            }

            if ($problems) {
                throw ValidationException::withMessages(['cart' => $problems]);
            }

            $order = Order::create([
                'number' => 'TMP-'.bin2hex(random_bytes(6)),
                'user_id' => $user->id,
                'status' => Order::PENDING,
                'total' => round($total, 2),
                'payment_method' => $details['payment_method'],
                'shipping_name' => $details['shipping_name'],
                'shipping_address' => $details['shipping_address'],
                'shipping_city' => $details['shipping_city'],
                'shipping_postal_code' => $details['shipping_postal_code'],
                'shipping_country' => $details['shipping_country'],
                'notes' => $details['notes'] ?? null,
            ]);
            $order->update(['number' => 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
                $line['product']->decrement('stock_quantity', $line['quantity']);
            }

            return $order;
        });
    }
}
