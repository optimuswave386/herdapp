<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-fg-brand hover:underline">← Back to shop</a>
            @if (auth()->user()?->is_admin)
                <a href="{{ route('products.edit', $product) }}" class="rounded-lg border border-default bg-white px-3 py-1.5 text-sm font-semibold text-heading shadow-xs hover:bg-neutral-secondary-soft">Edit product</a>
            @endif
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8"
         x-data="productPage({ id: {{ $product->id }}, name: @js($product->name), price: {{ (float) $product->price }}, stock: {{ (int) $product->stock_quantity }} })">
        <div class="card grid gap-8 p-6 md:grid-cols-2">
            <div class="aspect-square overflow-hidden rounded-lg bg-neutral-tertiary">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="size-full object-cover">
                @else
                    <div class="grid size-full place-items-center text-sm text-body-subtle">No image</div>
                @endif
            </div>

            <div class="flex flex-col">
                @if ($product->category)
                    <p class="text-sm font-medium text-fg-brand">{{ $product->category }}</p>
                @endif
                <h1 class="mt-1 text-3xl font-bold text-heading">{{ $product->name }}</h1>
                @unless ($product->isActive())
                    <p class="mt-2 inline-flex w-fit rounded-full bg-warning-soft px-2.5 py-0.5 text-xs font-semibold text-fg-warning">Inactive · hidden from the shop</p>
                @endunless

                <p class="mt-4 text-3xl font-extrabold tabular-nums text-heading">${{ number_format($product->price, 2) }}</p>

                <p class="mt-2 text-sm font-medium {{ $product->stock_quantity > 5 ? 'text-fg-success' : ($product->stock_quantity > 0 ? 'text-fg-warning' : 'text-fg-danger') }}">
                    @if ($product->stock_quantity > 5) In stock
                    @elseif ($product->stock_quantity > 0) Only {{ $product->stock_quantity }} left
                    @else Out of stock
                    @endif
                </p>

                @if ($product->description)
                    <p class="mt-5 whitespace-pre-line text-body">{{ $product->description }}</p>
                @endif

                <div class="mt-auto pt-6">
                    @if ($product->isActive() && $product->stock_quantity > 0)
                        <div class="flex items-center gap-3">
                            <input type="number" min="1" :max="stock" x-model.number="qty" aria-label="Quantity"
                                   class="w-20 rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand">
                            <x-primary-button type="button" class="flex-1 px-6 py-3" @click="add()">Add to cart</x-primary-button>
                        </div>
                        <p x-show="added" x-cloak x-transition class="mt-3 text-sm font-medium text-fg-success">
                            Added to your cart. <a href="{{ route('shoppingcart.index') }}" class="underline">View cart</a>
                        </p>
                    @else
                        <button type="button" disabled class="w-full cursor-not-allowed rounded-lg bg-neutral-tertiary px-6 py-3 text-sm font-semibold text-body-subtle">Unavailable</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Writes to the same localStorage cart the shop page and navbar use.
            function productPage(p) {
                return {
                    qty: 1,
                    stock: p.stock,
                    added: false,
                    add() {
                        const qty = Math.max(1, Math.min(Number(this.qty) || 1, p.stock));
                        let cart = {};
                        try { cart = JSON.parse(localStorage.getItem('counts')) || {}; } catch (e) {}
                        const already = cart[p.id] ? Number(cart[p.id].quantity) : 0;
                        cart[p.id] = {
                            quantity: Math.min(already + qty, p.stock),
                            product_id: p.id,
                            product_name: p.name,
                            price: p.price,
                        };
                        localStorage.setItem('counts', JSON.stringify(cart));
                        window.dispatchEvent(new CustomEvent('cart-changed'));
                        this.added = true;
                        setTimeout(() => (this.added = false), 4000);
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
