<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-heading">Order {{ $order->number }}</h2>
            <x-order-status :status="$order->status" />
        </div>
    </x-slot>

    {{-- The order went through: empty the browser cart before the navbar reads it. --}}
    @if (session('order_placed'))
        <script>localStorage.removeItem('counts');</script>
    @endif

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-default bg-neutral-secondary-soft text-xs uppercase tracking-wider text-body-subtle">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">Item</th>
                        <th class="px-5 py-3 text-end font-semibold">Price</th>
                        <th class="px-5 py-3 text-end font-semibold">Qty</th>
                        <th class="px-5 py-3 text-end font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="px-5 py-3 font-medium text-heading">{{ $item->product_name }}</td>
                            <td class="px-5 py-3 text-end tabular-nums">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-5 py-3 text-end tabular-nums">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-end tabular-nums">${{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-default">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-end font-semibold text-heading">Total</td>
                        <td class="px-5 py-3 text-end text-base font-bold tabular-nums text-heading">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <div class="grid gap-6 sm:grid-cols-2">
            <section class="card p-5">
                <h3 class="card-title mb-2">Shipping to</h3>
                <address class="text-sm not-italic leading-6 text-body">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                    {{ $order->shipping_country }}
                </address>
            </section>
            <section class="card p-5">
                <h3 class="card-title mb-2">Details</h3>
                <dl class="space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-body-subtle">Placed</dt><dd>{{ $order->created_at->format('M j, Y g:i A') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-body-subtle">Payment</dt><dd>{{ $order->paymentLabel() }}</dd></div>
                    @if ($order->notes)
                        <div><dt class="text-body-subtle">Notes</dt><dd class="mt-1">{{ $order->notes }}</dd></div>
                    @endif
                </dl>
            </section>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-fg-brand hover:underline">← All my orders</a>
            @if (auth()->user()->is_admin)
                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-fg-brand hover:underline">Manage this order</a>
            @endif
        </div>
    </div>
</x-app-layout>
