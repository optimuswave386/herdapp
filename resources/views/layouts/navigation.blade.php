<script>
    function refreshCartData() {
        let cartData = JSON.parse(localStorage.getItem('counts')) || {};
        return {
            cartitems: [],
            itemCount: 0,
            init() {
                // Fetch initial cart count from backend on load
                this.cartitems = cartData;
                this.refreshcart();
                this.getCartCount();
            },
            async refreshcart() {
                cartData = JSON.parse(localStorage.getItem('counts')) || {};
                this.cartitems = cartData;
            },
            async getCartCount() {
                try {
                    const response = await fetch('/api/shoppingcart/count', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cart: this.cartitems })
                    });
                    const data = await response.json();
                    this.itemCount = data.count;
                } catch (error) {
                    console.error('Error fetching cart count:', error);
                }
            }
        }
    }
</script>

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-default bg-white/90 backdrop-blur" aria-label="Main">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            <!-- Logo + primary links -->
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex shrink-0 items-center" aria-label="Home">
                    <x-laracrafts class="h-9 w-auto" />
                </a>

                <div class="hidden h-16 items-stretch gap-6 sm:flex">
                    <x-nav-link :href="url('/')" :active="request()->is('/')">{{ __('Home') }}</x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*') && ! request()->is('admin/*')">{{ __('Shop') }}</x-nav-link>
                    @auth
                        <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">{{ __('Posts') }}</x-nav-link>
                    @endauth
                    @if (Auth::user()?->is_admin)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->is('admin*')">{{ __('Admin') }}</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1 sm:gap-2">

                <!-- Cart -->
                <div class="relative" x-data="refreshCartData()" x-init="init()" @cart-changed.window="refreshcart(); getCartCount()">
                    <button id="myCartDropdownButton1" data-dropdown-toggle="myCartDropdown1" data-dropdown-placement="bottom-end" type="button"
                            class="relative inline-flex items-center justify-center rounded-lg p-2.5 text-body-subtle transition hover:bg-neutral-tertiary hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                        <span class="sr-only">Shopping Cart</span>
                        <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                        </svg>
                        <span x-show="itemCount > 0" x-text="itemCount" x-cloak
                              class="absolute -end-0.5 -top-0.5 grid min-w-5 place-items-center rounded-full bg-brand px-1 text-[11px] font-bold leading-5 text-white"></span>
                    </button>

                    <div id="myCartDropdown1" class="z-50 hidden w-80 max-w-sm space-y-4 overflow-hidden rounded-lg border border-default bg-white p-4 shadow-lg">
                        <x-dropdown-shoppingcart>
                            <x-slot name="cartItems">
                                <div class="mb-2 text-lg font-semibold text-heading">Shopping Cart</div>
                            </x-slot>
                        </x-dropdown-shoppingcart>
                    </div>
                </div>

                @auth
                    <!-- User menu -->
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-lg py-1.5 pe-2 ps-1.5 text-sm font-medium text-heading transition hover:bg-neutral-tertiary focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                                    <span class="grid size-8 place-items-center rounded-full bg-brand-soft text-sm font-bold uppercase text-fg-brand-strong">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                                    <span class="max-w-32 truncate">{{ Auth::user()->name }}</span>
                                    <svg class="size-4 text-body-subtle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="url('/@'.Auth::user()->name)">{{ __('View Profile') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('orders.index')">{{ __('My orders') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">{{ __('Edit Profile') }}</x-dropdown-link>
                                @if (Auth::user()->is_admin)
                                    <x-dropdown-link :href="route('admin.dashboard')">{{ __('Admin dashboard') }}</x-dropdown-link>
                                @endif

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-heading hover:bg-neutral-tertiary">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-brand-strong focus:outline-none focus:ring-4 focus:ring-brand-medium">{{ __('Register') }}</a>
                    </div>
                @endauth

                <!-- Hamburger -->
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2.5 text-body-subtle hover:bg-neutral-tertiary hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary sm:hidden" :aria-expanded="open" aria-label="Menu">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive menu -->
    <div x-show="open" x-cloak x-transition.opacity class="border-t border-default bg-white sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">{{ __('Home') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*') && ! request()->is('admin/*')">{{ __('Shop') }}</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')">{{ __('Posts') }}</x-responsive-nav-link>
            @endauth
            @if (Auth::user()?->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->is('admin*')">{{ __('Admin') }}</x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-default px-4 py-3">
            @auth
                <div class="mb-2">
                    <div class="text-base font-medium text-heading">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-body-subtle">{{ Auth::user()->email }}</div>
                </div>
                <div class="space-y-1">
                    <x-responsive-nav-link :href="url('/@'.Auth::user()->name)">{{ __('View Profile') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.index')">{{ __('My orders') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">{{ __('Edit Profile') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 rounded-lg border border-default px-4 py-2.5 text-center text-sm font-semibold text-heading">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-brand px-4 py-2.5 text-center text-sm font-semibold text-white">{{ __('Register') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
