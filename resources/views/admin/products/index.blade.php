@extends('layouts.admin')

@section('title', 'Products')
@section('heading', 'Products')
@section('subheading', number_format($products->total()).' '.Str::plural('product', $products->total()).' in the catalogue')

@section('actions')
    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-strong focus:outline-none focus:ring-4 focus:ring-brand-medium">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add product
    </a>
@endsection

@section('admin')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead class="border-b border-default bg-neutral-secondary-soft text-xs uppercase tracking-wider text-body-subtle">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-start font-semibold">Product</th>
                        <th scope="col" class="px-5 py-3 text-start font-semibold">Category</th>
                        <th scope="col" class="px-5 py-3 text-start font-semibold">Status</th>
                        <th scope="col" class="px-5 py-3 text-end font-semibold">Price</th>
                        <th scope="col" class="px-5 py-3 text-end font-semibold">Stock</th>
                        <th scope="col" class="px-5 py-3 text-end font-semibold"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse ($products as $product)
                        @php
                            $stock = (int) $product->stock_quantity;
                            $stockClass = $stock <= 0 ? 'bg-danger-soft text-fg-danger-strong' : ($stock <= 10 ? 'bg-warning-soft text-fg-warning' : 'bg-success-soft text-fg-success-strong');
                        @endphp
                        <tr class="transition hover:bg-neutral-secondary-soft">
                            <td class="px-5 py-3">
                                <a href="{{ route('products.edit', $product) }}" class="font-medium text-heading hover:text-fg-brand">{{ $product->name }}</a>
                                <p class="max-w-xs truncate text-xs text-body-subtle">{{ $product->description }}</p>
                            </td>
                            <td class="px-5 py-3">{{ $product->category ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($product->status ?? 'active') === 'active' ? 'bg-success-soft text-fg-success-strong' : 'bg-neutral-tertiary text-body' }}">
                                    {{ ucfirst($product->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-end tabular-nums">${{ number_format($product->price, 2) }}</td>
                            <td class="px-5 py-3 text-end">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold tabular-nums {{ $stockClass }}">{{ $stock }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-end">
                                <a href="{{ route('products.edit', $product) }}" class="text-sm font-medium text-fg-brand hover:underline">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="ms-3 inline"
                                      onsubmit="return confirm('Delete “{{ addslashes($product->name) }}”? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-fg-danger hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-body-subtle">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection
