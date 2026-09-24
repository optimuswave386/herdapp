@extends('layouts.admin')

@section('title', 'Orders')
@section('heading', 'Orders')
@section('subheading', 'Revenue counts orders that are paid, shipped or completed.')

@php
    $tabs = ['' => 'All'] + collect(\App\Models\Order::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all();
@endphp

@section('admin')
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($tabs as $value => $label)
            @php
                $active = (string) $status === (string) $value;
                $n = $value === '' ? $counts->sum() : ($counts[$value] ?? 0);
            @endphp
            <a href="{{ route('admin.orders.index', $value === '' ? [] : ['status' => $value]) }}"
               class="rounded-full border px-3 py-1 text-sm font-medium transition {{ $active ? 'border-brand bg-brand-softer text-fg-brand-strong' : 'border-default bg-white text-body hover:bg-neutral-tertiary' }}">
                {{ $label }} <span class="ms-1 text-xs opacity-70">{{ $n }}</span>
            </a>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-default bg-neutral-secondary-soft text-xs uppercase tracking-wider text-body-subtle">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">Order</th>
                        <th class="px-5 py-3 text-start font-semibold">Customer</th>
                        <th class="px-5 py-3 text-start font-semibold">Status</th>
                        <th class="px-5 py-3 text-end font-semibold">Total</th>
                        <th class="px-5 py-3 text-end font-semibold">Placed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-neutral-secondary-soft">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-fg-brand hover:underline">{{ $order->number }}</a>
                                <span class="block text-xs text-body-subtle">{{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-heading">{{ $order->shipping_name }}</span>
                                <span class="block text-xs text-body-subtle">{{ $order->user?->email ?? 'Deleted account' }}</span>
                            </td>
                            <td class="px-5 py-3"><x-order-status :status="$order->status" /></td>
                            <td class="px-5 py-3 text-end font-medium tabular-nums">${{ number_format($order->total, 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-end text-body-subtle">{{ $order->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-body-subtle">No orders here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
