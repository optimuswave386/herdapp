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

    <script>
    function cartHandler() {
        return {
            itemCount: 0,
            cartitems: [],
            counts: JSON.parse(localStorage.getItem('counts')) || {},
            init() {
                // Fetch initial cart count from backend on load
                this.getCartCount();
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
                        body: JSON.stringify({ cart: this.counts })
                    });
                    const data = await response.json();
                    this.itemCount = data.count;
                } catch (error) {
                    console.error('Error fetching cart count:', error);
                }
            },
            async addToCart(productId) {
                const response = await fetch('/api/shoppingcart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId, user_id: {{ auth()->id() || 5 }}, quantity: 1 })
                });
                if (response.ok) { 
                    this.itemCount++; // Update count immediately in UI
                    response.json().then(data => { 
                        //console.log('Product added to cart:', data.cart);
                        //console.log(data.cart[productId].product_id, data.cart[productId].quantity);
                        //console.log(this.cartitems.find(cart => cart.product_id === data.cart[productId].product_id));
                        //console.log(this.itemCount);
                        //console.log(this.cartitems);
                        if (!this.cartitems.find(cart => cart.product_id === data.cart[productId].product_id)) { 
                            this.cartitems.push({ product_id: data.cart[productId].product_id, name: data.cart[productId].name, quantity: data.cart[productId].quantity, price: data.cart[productId].price });
                            if(!this.counts[productId]) {
                                this.counts[productId] = { quantity: data.cart[productId].quantity, product_id: data.cart[productId].product_id, product_name: data.cart[productId].name, price: data.cart[productId].price };
                            } else {
                                this.counts[productId].quantity += data.cart[productId].quantity;
                            }
                            //  console.log('Added a cart item:', this.cartitems);
                        } else { 
                            item = this.cartitems.find(cart => cart.product_id === data.cart[productId].product_id);
                            this.cartitems = this.cartitems.filter(cart => cart.product_id !== data.cart[productId].product_id);
                            item.quantity += 1;
                            this.cartitems.push({ product_id: data.cart[productId].product_id, name: data.cart[productId].name, quantity: item.quantity, price: data.cart[productId].price });
                            if(!this.counts[productId]) {
                                this.counts[productId] = { quantity: item.quantity, product_id: data.cart[productId].product_id, product_name: data.cart[productId].name, price: data.cart[productId].price };
                            } else {
                                this.counts[productId].quantity += 1;
                            }
                            //console.log('Updated cart item quantity:', this.cartitems);
                        }
                        // Optionally store cart data to middleware\backend
                        this.storeCartData();
                        localStorage.setItem('counts', JSON.stringify(this.counts));
                    });
                } else { 
                    alert('Response status: ' + response.status);
                    alert('Error adding product to cart.');
                    this.cartdata = [];
                }
                window.dispatchEvent(new Event('cartupdated'));
            },
            async storeCartData() {
                // Optionally implement storing cart data to backend
                const response = await fetch('/api/shoppingcart/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cart: this.cartitems })
                });
                if (response.ok) {
                    response.json().then(data => {
                        //console.log('Cart data stored response:', data.message);
                        console.log('Cart data stored response:', data.cart);
                    });
                    //console.log('Cart data stored successfully');
                } else {
                    console.error('Failed to store cart data');
                }
            },
            async clearCart() {
                const response = await fetch('/api/shoppingcart/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    this.cartitems = [];
                    this.itemCount = 0;
                } else {
                    console.error('Failed to clear cart');
                }
            }
        };
    }
    </script>


    <div class="py-12" x-data="cartHandler()" @cartupdated.window="window.location.reload();">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-1 sm:p-1 bg-white shadow sm:rounded-lg">

                <section class="w-full bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-4">
                <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                    <!-- Heading -->
                    <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
                    <div>
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">All Products</h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
                        <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                        </svg>
                        Sort
                        <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                        </button>
                        <div id="dropdownSort1" class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700" data-popper-placement="bottom">
                        <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400" aria-labelledby="sortDropdownButton">
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> The most popular </a>
                            </li>
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> Newest </a>
                            </li>
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> Increasing price </a>
                            </li>
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> Decreasing price </a>
                            </li>
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> No. reviews </a>
                            </li>
                            <li>
                            <a href="#" class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"> Discount % </a>
                            </li>
                        </ul>
                        </div>
                    </div>
                    </div>
                    <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
                        
                        <!-- Product card -->
                        @foreach ($products as $product)
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="h-56 w-full">
                        <a href="#">
                            <img class="mx-auto h-full dark:hidden" src="{{ asset($product->image_url) }}" alt="" />
                            <img class="mx-auto hidden h-full dark:block" src="{{ asset($product->image_url) }}" alt="" />
                        </a>
                        </div>
                        <div class="pt-6">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <span class="me-2 rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300"> Up to 35% off </span>

                            <div class="flex items-center justify-end gap-1">
                            <button type="button" data-tooltip-target="tooltip-quick-look" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only"> Quick look </span>
                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                            <div id="tooltip-quick-look" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700" data-popper-placement="top">
                                Quick look
                                <div class="tooltip-arrow" data-popper-arrow=""></div>
                            </div>

                            <button type="button" data-tooltip-target="tooltip-add-to-favorites" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only"> Add to Favorites </span>
                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                </svg>
                            </button>
                            <div id="tooltip-add-to-favorites" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700" data-popper-placement="top">
                                Add to favorites
                                <div class="tooltip-arrow" data-popper-arrow=""></div>
                            </div>
                            </div>
                        </div>

                        {{ $product->category }} <br />
                        <a href="{{ route('products.show', $product) }}" class="text-lg font-semibold leading-tight text-gray-900 hover:underline dark:text-white">{{ $product->name }}</a>

                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex items-center">
                            <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                            </svg>

                            <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                            </svg>

                            <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                            </svg>

                            <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                            </svg>

                            <svg class="h-4 w-4 text-yellow-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                            </svg>
                            </div>

                            <p class="text-sm font-medium text-gray-900 dark:text-white">5.0</p>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">(455)</p>
                        </div>

                        <ul class="mt-2 flex items-center gap-4">
                            <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fast Delivery</p>
                            </li>

                            <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1h-1M3 18v-7c0-.6.4-1 1-1h11c.6 0 1 .4 1 1v7c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Best Price</p>
                            </li>
                        </ul>

                        <div class="mt-4 flex items-center justify-between gap-4">
                            <p class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</p>
                            <button type="button" @click="addToCart({{ $product->id }})" class="inline-flex items-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium dark:text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 border border-1-primary-700">
                            <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                            </svg>
                            Add to cart
                            </button>
                        </div>
                        </div>
                    </div>
                    @endforeach
                    <!-- End of product card -->

                </div>
                    
                <div class="w-full text-center">
                    <!-- Pagination -->
                    {{-- {{ $products->links() }} --}}
                    <!-- End of Pagination -->
                </div>
                
                </section>

            </div>
        </div>
    </div>

</x-app-layout>