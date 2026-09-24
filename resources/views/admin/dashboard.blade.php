@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Sales, users and catalogue at a glance.')

@php
    $money = fn ($v) => '$'.number_format($v, 2);
    $trend = fn ($pct, $fallback) => $pct === null ? $fallback : (($pct >= 0 ? '▲ ' : '▼ ').abs($pct).'% vs previous 30 days');

    $salesKpis = [
        [
            'label' => 'Revenue · 30 days',
            'value' => $money($sales['revenue30']),
            'note'  => $trend($sales['revenueGrowth'], 'Paid, shipped and completed orders'),
            'good'  => ($sales['revenueGrowth'] ?? 0) >= 0,
            'icon'  => 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label' => 'Orders · 30 days',
            'value' => number_format($sales['orders30']),
            'note'  => $sales['pendingOrders'].' pending '.Str::plural('order', $sales['pendingOrders']).' to process',
            'good'  => $sales['pendingOrders'] === 0,
            'icon'  => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        [
            'label' => 'Average order',
            'value' => $money($sales['avgOrder']),
            'note'  => 'Per paid order · 30 days',
            'good'  => true,
            'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
        [
            'label' => 'Needs restocking',
            'value' => number_format($stats['lowStock'] + $stats['outOfStock']),
            'note'  => $stats['outOfStock'].' out of stock · '.$stats['lowStock'].' low (≤ '.$lowStockThreshold.')',
            'good'  => ($stats['lowStock'] + $stats['outOfStock']) === 0,
            'icon'  => 'M12 9v4m0 4h.01M10.3 3.9L2.4 17.5A2 2 0 004.1 20.5h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z',
        ],
    ];

    $audienceKpis = [
        [
            'label' => 'Total users',
            'value' => number_format($stats['usersTotal']),
            'note'  => $trend($stats['usersGrowth'], '+'.$stats['usersLast30'].' in the last 30 days'),
            'good'  => ($stats['usersGrowth'] ?? 0) >= 0,
            'icon'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-6.13a4 4 0 11-8 0 4 4 0 018 0z',
        ],
        [
            'label' => 'Products',
            'value' => number_format($stats['productsTotal']),
            'note'  => $stats['productsActive'].' active in the shop',
            'good'  => true,
            'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'label' => 'Community',
            'value' => number_format($stats['followsTotal']),
            'note'  => 'follows · '.number_format($stats['postsTotal']).' posts',
            'good'  => true,
            'icon'  => 'M4.3 6.3a4.5 4.5 0 016.4 0L12 7.6l1.3-1.3a4.5 4.5 0 116.4 6.4L12 20.4 4.3 12.7a4.5 4.5 0 010-6.4z',
        ],
    ];
@endphp



@section('admin')
    {{-- ============ Sales ============ --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($salesKpis as $kpi)
            @include('admin.partials.kpi', ['kpi' => $kpi])
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-5 lg:col-span-2">
            <h2 class="card-title">Revenue</h2>
            <p class="mb-4 text-sm text-body-subtle">Paid, shipped and completed orders per month, last 6 months</p>
            <x-chart-component chartTitle="Revenue" :labels="$revenueLabels" :chartData="$revenueData" type="line" prefix="$" :height="280" />
        </section>

        <section class="card p-5">
            <h2 class="card-title">Orders by status</h2>
            <p class="mb-4 text-sm text-body-subtle">All orders to date</p>
            <x-chart-component chartTitle="Orders" :labels="$statusLabels" :chartData="$statusData" :colors="$statusPalette" type="doughnut" :height="280" />
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-5 lg:col-span-2">
            <h2 class="card-title">Top sellers</h2>
            <p class="mb-4 text-sm text-body-subtle">Revenue by product, last 30 days</p>
            <x-chart-component chartTitle="Revenue" :labels="$topLabels" :chartData="$topData" type="bar" prefix="$" :colors="['#10b981']" :height="330" />
        </section>

        <section class="card p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="card-title">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-fg-brand hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-default">
                @forelse ($recentOrders as $order)
                    <li class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <a href="{{ route('admin.orders.show', $order) }}" class="block truncate text-sm font-medium text-heading hover:text-fg-brand">{{ $order->number }}</a>
                            <span class="block truncate text-xs text-body-subtle">{{ $order->shipping_name }} · {{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="shrink-0 text-end">
                            <span class="block text-sm font-semibold tabular-nums text-heading">{{ $money($order->total) }}</span>
                            <x-order-status :status="$order->status" class="!px-2 !py-0 !text-[11px]" />
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-body-subtle">No orders yet.</li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- ============ Users & catalogue ============ --}}
    <h2 class="mb-4 mt-10 text-lg font-semibold text-heading">Users &amp; catalogue</h2>

    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ($audienceKpis as $kpi)
            @include('admin.partials.kpi', ['kpi' => $kpi])
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-5 lg:col-span-2">
            <h2 class="card-title">New users</h2>
            <p class="mb-4 text-sm text-body-subtle">Sign-ups per month, last 6 months</p>
            <x-chart-component chartTitle="New users" :labels="$signupLabels" :chartData="$signupData" type="line" :height="280" />
        </section>

        <section class="card p-5">
            <h2 class="card-title">Products by category</h2>
            <p class="mb-4 text-sm text-body-subtle">Top categories in the catalogue</p>
            <x-chart-component chartTitle="Products" :labels="$categoryLabels" :chartData="$categoryData" type="doughnut" :height="280" />
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card p-5 lg:col-span-2">
            <h2 class="card-title">Lowest stock</h2>
            <p class="mb-4 text-sm text-body-subtle">The 8 products closest to selling out</p>
            <x-chart-component chartTitle="Units in stock" :labels="$stockLabels" :chartData="$stockData" type="bar" :colors="['#f59e0b']" :height="320" />
        </section>

        <section class="card p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="card-title">Newest users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-fg-brand hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-default">
                @forelse ($recentUsers as $user)
                    <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-soft text-sm font-bold uppercase text-fg-brand-strong">{{ mb_substr($user->name, 0, 1) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-heading">{{ $user->name }}</p>
                            <p class="truncate text-xs text-body-subtle">{{ $user->email }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-body-subtle">{{ $user->created_at?->diffForHumans(short: true) }}</span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-body-subtle">No users yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
