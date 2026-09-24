@props(['cart'])

<script type="text/javascript">

    console.log("cart data on shopping cart page ", @json($cart));
    const cartData = JSON.parse(localStorage.getItem('counts')) || {};
    //const cartData = @json($cart);
        
    function showCartData() {
        return {
            cartitems: [],
            subtotalAmount: 0,
            init() {
                // Fetch initial cart count from backend on load
                this.cartitems = cartData;
            }            
        }
    }
    function subtotal(cartitems) {
        let total = 0;
        for (const product of Object.values(cartitems || {})) {
            total += product.price * product.quantity;
        }
        return total;
    }
    function updateCart(productId, updatedQuantity) {
        // Update cart count in local storage
        if (this.cartitems[productId]) {
            this.cartitems[productId].quantity = updatedQuantity;
        }
        localStorage.setItem('counts', JSON.stringify(this.cartitems));
    }

</script>

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
            @guest
            <nav>
                <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    | 
                <a href="{{ route('register') }}">{{ __('Register') }}</a>
            </nav>
            @endguest            
        </div>

    </x-slot>

    <div class="py-12" x-data="showCartData()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                <div class="max-w-xl">

                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Shopping Cart') }}
                    </h2>
                    
                    <br />
                    
                    <p>Total: <span x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(subtotal(cartitems))"></span></p>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        
                            <template x-if="Object.keys(cartitems || {}).length === 0">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-center">Your cart is empty.</td>
                                </tr>
                            </template>

                            <template x-for="(product,index) in Object.values(cartitems || {})" :key="index">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><div x-text="product.product_name"></div></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- <div x-text="product.quantity"></div> --}}
                                        <input type="number" name="quantity" x-model="product.quantity" min="1" class="w-16 border border-gray-300 rounded-sm px-2 py-1" disabled />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format((product.price))"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format((product.price * product.quantity))"></div>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>

                    <div class="mt-4">
                        <a href="{{ route('shoppingcart.checkout') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Buy Now
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>

