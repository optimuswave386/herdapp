@extends('layouts.admin')

@section('title', 'Edit '.$product->name)
@section('heading', 'Edit product')
@section('subheading', $product->name)

@section('actions')
    <a href="{{ route('products.show', $product) }}" class="text-sm font-medium text-fg-brand hover:underline">View in shop →</a>
@endsection

@section('admin')
    <div class="card max-w-3xl p-6">
        @include('products._form', ['action' => route('products.update', $product), 'method' => 'PATCH'])
    </div>

    <div class="card mt-6 max-w-3xl border-danger-subtle p-6">
        <h2 class="card-title text-fg-danger-strong">Delete product</h2>
        <p class="mt-1 text-sm text-body-subtle">Removes it from the shop. Past orders keep their own record of what was sold.</p>
        <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-4"
              onsubmit="return confirm('Delete “{{ addslashes($product->name) }}”? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white hover:bg-danger-strong focus:outline-none focus:ring-4 focus:ring-danger-medium">Delete product</button>
        </form>
    </div>
@endsection
