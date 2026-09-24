<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') · @endif{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="flex min-h-full flex-col bg-neutral-secondary-soft font-sans text-body antialiased">
        <a href="#main" class="sr-only rounded-lg bg-brand px-4 py-2 text-white focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50">
            Skip to content
        </a>

        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="border-b border-default bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        @include('layouts.partials.flash')

        <!-- Page Content -->
        <main id="main" class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-default bg-white">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-6 text-sm text-body-subtle sm:flex-row sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                <nav class="flex gap-5" aria-label="Footer">
                    <a href="{{ url('/about') }}" class="hover:text-fg-brand">About</a>
                    <a href="{{ route('products.index') }}" class="hover:text-fg-brand">Shop</a>
                </nav>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
