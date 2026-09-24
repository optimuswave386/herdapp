<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-heading">My orders</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-default bg-neutral-secondary-soft text-xs uppercase tracking-wider text-body-subtle">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">Order</th>
                        <th class="px-5 py-3 text-start font-semibold">Placed</th>
                        <th class="px-5 py-3 text-start font-semibold">Status</th>
                        <th class="px-5 py-3 text-end font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-neutral-secondary-soft">
                            <td class="px-5 py-3">
                                <a href="{{ route('orders.show', $order) }}" class="font-medium text-fg-brand hover:underline">{{ $order->number }}</a>
                                <span class="block text-xs text-body-subtle">{{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-body-subtle">{{ $order->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-3"><x-order-status :status="$order->status" /></td>
                            <td class="px-5 py-3 text-end font-medium tabular-nums">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-body-subtle">
                                You haven't placed an order yet.
                                <a href="{{ route('products.index') }}" class="font-medium text-fg-brand hover:underline">Browse the shop</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
