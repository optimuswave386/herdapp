{{--
    Admin shell: sidebar + content area, inside the shared master layout.

    Usage in a page:
        @extends('layouts.admin')
        @section('title', 'Users')
        @section('heading', 'Users')
        @section('subheading', 'Everyone with an account')
        @section('actions') ...optional buttons... @endsection
        @section('admin') ...page body... @endsection
--}}
@extends('layouts.master')

@php
    $items = [
        ['route' => 'admin.dashboard',      'match' => 'admin.dashboard',  'label' => 'Dashboard', 'icon' => 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
        ['route' => 'admin.users.index',    'match' => 'admin.users.*',    'label' => 'Users',     'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 10-6 0m-12 0a3 3 0 116 0'],
        ['route' => 'admin.orders.index',   'match' => 'admin.orders.*',   'label' => 'Orders',    'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
        ['route' => 'admin.products.index', 'match' => ['admin.products.*', 'products.create', 'products.edit'], 'label' => 'Products',  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-[90rem] lg:flex">

        <!-- Sidebar (a scrolling pill bar on small screens) -->
        <aside class="border-b border-default bg-white lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:w-60 lg:shrink-0 lg:overflow-y-auto lg:border-b-0 lg:border-e" aria-label="Admin">
            <nav class="flex gap-1 overflow-x-auto p-3 lg:flex-col lg:p-4">
                <p class="hidden px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wider text-body-subtle lg:block">Admin</p>

                @foreach ($items as $item)
                    @php $active = request()->routeIs(...(array) $item['match']); @endphp
                    <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                       class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-brand-softer text-fg-brand-strong' : 'text-body hover:bg-neutral-tertiary hover:text-heading' }}">
                        <svg class="size-5 shrink-0 {{ $active ? 'text-fg-brand' : 'text-body-subtle' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <div class="mx-2 my-2 hidden border-t border-default lg:block"></div>
                <a href="{{ route('products.index') }}" class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-body transition hover:bg-neutral-tertiary hover:text-heading">
                    <svg class="size-5 shrink-0 text-body-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to shop
                </a>
            </nav>
        </aside>

        <!-- Content -->
        <div class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            @hasSection('heading')
                <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-heading">@yield('heading')</h1>
                        @hasSection('subheading')
                            <p class="mt-1 text-sm text-body-subtle">@yield('subheading')</p>
                        @endif
                    </div>
                    @yield('actions')
                </div>
            @endif

            @yield('admin')
        </div>
    </div>
@endsection
