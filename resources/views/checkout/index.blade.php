<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-heading">{{ __('Checkout') }}</h2>
    </x-slot>

    <div class="py-8" x-data="checkoutPage()" x-init="init()">
        <form method="POST" action="{{ route('shoppingcart.place') }}" class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            @csrf
            {{-- The cart lives in the browser. Only product ids and quantities are read from it; prices are re-read on the server. --}}
            <input type="hidden" name="cart" :value="JSON.stringify(items)">

            <div class="space-y-6 lg:col-span-2">
                @if ($errors->any())
                    <div class="rounded-lg border border-danger-subtle bg-danger-soft p-4 text-sm text-fg-danger-strong" role="alert">
                        <p class="mb-1 font-semibold">We couldn't place your order:</p>
                        <ul class="list-disc space-y-0.5 ps-5">
                            @foreach ($errors->unique() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="card p-6">
                    <h3 class="card-title mb-4">Shipping details</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input-label for="shipping_name" value="Full name" />
                            <x-text-input id="shipping_name" name="shipping_name" class="mt-1 block w-full" :value="old('shipping_name', auth()->user()->name)" required autocomplete="name" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="shipping_address" value="Address" />
                            <x-text-input id="shipping_address" name="shipping_address" class="mt-1 block w-full" :value="old('shipping_address')" required autocomplete="street-address" />
                        </div>
                        <div>
                            <x-input-label for="shipping_city" value="City" />
                            <x-text-input id="shipping_city" name="shipping_city" class="mt-1 block w-full" :value="old('shipping_city')" required autocomplete="address-level2" />
                        </div>
                        <div>
                            <x-input-label for="shipping_postal_code" value="Postal code" />
                            <x-text-input id="shipping_postal_code" name="shipping_postal_code" class="mt-1 block w-full" :value="old('shipping_postal_code')" required autocomplete="postal-code" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="shipping_country" value="Country" />
                            <x-text-input id="shipping_country" name="shipping_country" class="mt-1 block w-full" :value="old('shipping_country')" required autocomplete="country-name" />
                        </div>
                    </div>
                </section>

                <section class="card p-6">
                    <h3 class="card-title mb-4">Payment</h3>
                    <div class="space-y-3">
                        @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-default p-4 has-[:checked]:border-brand has-[:checked]:bg-brand-softer">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="text-brand focus:ring-brand" @checked(old('payment_method', 'pay_on_delivery') === $value)>
                                <span class="text-sm font-medium text-heading">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-body-subtle">No card details are collected here. Your order is confirmed once payment is received or delivered.</p>

                    <div class="mt-5">
                        <x-input-label for="notes" value="Order notes (optional)" />
                        <textarea id="notes" name="notes" rows="3" maxlength="1000" class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand">{{ old('notes') }}</textarea>
                    </div>
                </section>
            </div>

            <!-- Order summary -->
            <aside class="lg:col-span-1">
                <div class="card sticky top-24 p-6">
                    <h3 class="card-title mb-4">Order summary</h3>

                    <template x-if="count === 0">
                        <div class="py-6 text-center text-sm text-body-subtle">
                            <p>Your cart is empty.</p>
                            <a href="{{ route('products.index') }}" class="mt-2 inline-block font-medium text-fg-brand hover:underline">Continue shopping</a>
                        </div>
                    </template>

                    <ul class="divide-y divide-default" x-show="count > 0">
                        <template x-for="item in Object.values(items)" :key="item.product_id">
                            <li class="flex items-start justify-between gap-3 py-3 text-sm">
                                <span class="text-heading"><span x-text="item.product_name"></span> <span class="text-body-subtle">× <span x-text="item.quantity"></span></span></span>
                                <span class="shrink-0 font-medium tabular-nums" x-text="money(item.price * item.quantity)"></span>
                            </li>
                        </template>
                    </ul>

                    <div class="mt-4 flex items-center justify-between border-t border-default pt-4" x-show="count > 0">
                        <span class="text-base font-semibold text-heading">Estimated total</span>
                        <span class="text-xl font-bold tabular-nums text-heading" x-text="money(total)"></span>
                    </div>
                    <p class="mt-2 text-xs text-body-subtle" x-show="count > 0">Final prices and availability are confirmed when you place the order.</p>

                    <x-primary-button class="mt-5 w-full" x-bind:disabled="count === 0">Place order</x-primary-button>
                    <a href="{{ route('shoppingcart.index') }}" class="mt-3 block text-center text-sm font-medium text-fg-brand hover:underline">Back to cart</a>
                </div>
            </aside>
        </form>
    </div>

    @push('scripts')
        <script>
            function checkoutPage() {
                return {
                    items: {},
                    get count() { return Object.keys(this.items).length; },
                    get total() {
                        return Object.values(this.items).reduce((sum, i) => sum + Number(i.price) * Number(i.quantity), 0);
                    },
                    money(n) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(n); },
                    init() {
                        try { this.items = JSON.parse(localStorage.getItem('counts')) || {}; } catch (e) { this.items = {}; }
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
