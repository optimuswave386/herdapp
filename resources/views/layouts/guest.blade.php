<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-gradient-to-br from-brand-softer via-white to-slate-100 font-sans text-body antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="/" class="mb-6" aria-label="Home">
                <x-laracrafts class="h-16 w-auto" />
            </a>

            <div class="w-full overflow-hidden rounded-lg border border-default bg-white px-6 py-6 shadow-md sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-body-subtle">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</p>
        </div>
    </body>
</html>
