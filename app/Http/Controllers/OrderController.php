<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /** The signed-in customer's own orders. */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->withCount('items')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);

        return view('orders.show', ['order' => $order->load('items', 'user')]);
    }

    /** Place an order from the cart posted by the checkout page. */
    public function store(CheckoutRequest $request, CheckoutService $checkout)
    {
        $order = $checkout->placeOrder($request->user(), $request->validated(), $request->validated('items'));

        return redirect()->route('orders.show', $order)
            ->with('order_placed', true)
            ->with('success', "Thank you! Your order {$order->number} has been placed.");
    }
}
