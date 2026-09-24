<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * The browser sends its cart as JSON in `cart` (the shape kept in localStorage).
     * Only product_id and quantity are read from it; prices are never trusted.
     */
    protected function prepareForValidation(): void
    {
        $decoded = json_decode((string) $this->input('cart', '[]'), true);

        $items = collect(is_array($decoded) ? $decoded : [])
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item) => ['product_id' => $item['product_id'] ?? null, 'quantity' => $item['quantity'] ?? null])
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'between:1,99'],

            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Your cart is empty.',
            'items.min' => 'Your cart is empty.',
            'items.*.product_id.exists' => 'A product in your cart no longer exists.',
            'items.*.quantity.between' => 'Quantities must be between 1 and 99.',
        ];
    }
}
