
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (Auth::check())
                Welcome, {{ Auth::user()->name }}!
            @else
                {{ __('Welcome') }}
            @endif
        </h2>
        <div style="margin-top: -1.5rem; margin-left: auto; float: right;">
            {{-- @guest
            <nav>
                <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    | 
                <a href="{{ route('register') }}">{{ __('Register') }}</a>
            </nav>
            @endguest             --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">
                
                    {{ __('To view our product catalog, visit ') }}
                    <a href="{{ route('products.index') }}" class="text-blue-500 underline">Products</a>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>