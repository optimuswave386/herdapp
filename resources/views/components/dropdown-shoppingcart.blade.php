
<script type="text/javascript">
    function showCartData() {
        const cartData = JSON.parse(localStorage.getItem('counts')) || {};
        return {
            cartitems: [],
            subtotalAmount: 0,
            init() {
                // Fetch initial cart count from backend on load
                this.cartitems = cartData;
                this.subtotalAmount = this.subtotal();
            },
            subtotal() {
                let total = 0;
                for (const product of Object.values(this.cartitems || {})) {
                    total += product.price * product.quantity;
                }
                return total;
            }
        }
    }
    function removeFromCart(productId) {
        let cartData = JSON.parse(localStorage.getItem('counts')) || {};
        if (cartData[productId]) {
            delete cartData[productId];
            localStorage.setItem('counts', JSON.stringify(cartData));
            // Optionally, you can trigger a re-render or update the UI here
            location.reload(); // Simple way to refresh the component
        }
    }
</script>

{{ $slot }}

    <div x-data="showCartData()" x-init="init()">

        <span class="block text-sm font-medium text-gray-900 dark:text-white mb-2" x-text="subtotalAmount > 0 ? 'Shopping Cart (Total: $' + subtotalAmount + ')' : 'Shopping Cart'"></span>

        <template x-if="Object.keys(cartitems || {}).length === 0">
            <div class="grid grid-cols-2">
                Your cart is empty. 
            </div>
        </template>

        <template x-for="(product,index) in Object.values(cartitems || {})" :key="index">
        <div class="grid grid-cols-2" >
                <div>
                <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline"><div x-text="product.product_name"></div></a>
                <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400"><div x-text="product.price"></div></p>
                </div>
        
                <div class="flex items-center justify-end gap-6">
                <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: <div x-text="product.quantity"></div></p>
                
                <button @click="removeFromCart(product.product_id)" data-tooltip-target="tooltipRemoveItem1a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                    <span class="sr-only"> Remove </span>
                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="tooltipRemoveItem1a" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                    Remove item
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                </div>
        </div>
        </template>

        <template x-if="Object.keys(cartitems || {}).length > 0">
            <a href="#" @click="window.location.href = '{{ route('shoppingcart.index') }}'" title="" class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-strong focus:outline-none focus:ring-4 focus:ring-brand-medium" role="button"> Proceed to Checkout </a>
        </template>

    </div>

    {{-- <form method="POST" action="{{ route('shoppingcart.remove', ['id' => $item['id']]) }}">
        @csrf
        <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
            <span class="sr-only"> Remove </span>
            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
            </svg>
        </button>
    </form> --}}
                        