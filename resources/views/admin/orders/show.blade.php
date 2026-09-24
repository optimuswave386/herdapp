@extends('layouts.admin')

@section('title', 'Order '.$order->number)
@section('heading', 'Order '.$order->number)
@section('subheading', 'Placed '.$order->created_at->format('M j, Y g:i A').' by '.($order->user?->email ?? 'a deleted account'))

@section('actions')
    <x-order-status :status="$order->status" class="!px-3 !py-1 !text-sm" />
@endsection

@section('admin')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
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
                                <td class="px-5 py-3 font-medium text-heading">
                                    {{ $item->product_name }}
                                    @unless ($item->product_id)<span class="ms-1 text-xs font-normal text-body-subtle">(product deleted)</span>@endunless
                                </td>
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

            <section class="card p-5">
                <h2 class="card-title mb-2">Ship to</h2>
                <address class="text-sm not-italic leading-6 text-body">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                    {{ $order->shipping_country }}
                </address>
                @if ($order->notes)
                    <p class="mt-3 border-t border-default pt-3 text-sm text-body"><span class="font-medium text-heading">Customer note:</span> {{ $order->notes }}</p>
                @endif
            </section>
        </div>

        <aside>
            <section class="card p-5">
                <h2 class="card-title mb-1">Status</h2>
                <p class="mb-4 text-sm text-body-subtle">Payment: {{ $order->paymentLabel() }}</p>

                @if ($order->status === \App\Models\Order::CANCELLED)
                    <p class="rounded-lg bg-neutral-secondary-soft p-3 text-sm text-body">This order was cancelled and its stock was returned. Cancelled orders can't be reopened.</p>
                @else
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="block w-full rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand">
                            @foreach (\App\Models\Order::STATUSES as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <x-primary-button class="w-full">Update status</x-primary-button>
                        <p class="text-xs text-body-subtle">Cancelling puts the items back in stock.</p>
                    </form>
                @endif
            </section>
            <a href="{{ route('admin.orders.index') }}" class="mt-4 inline-block text-sm font-medium text-fg-brand hover:underline">← All orders</a>
        </aside>
    </div>
@endsection
