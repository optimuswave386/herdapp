{{-- Shared by products/create and products/edit.
     Expects: $product, $categories, $action, and optionally $method (POST or PATCH). --}}
@php $method = $method ?? 'POST'; @endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    @if ($errors->any())
        <div class="rounded-lg border border-danger-subtle bg-danger-soft p-4 text-sm text-fg-danger-strong" role="alert">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-1 list-disc ps-5">
                @foreach ($errors->unique() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $product->name)" required />
        </div>

        <div>
            <x-input-label for="category" value="Category" />
            <x-text-input id="category" name="category" class="mt-1 block w-full" list="category-options" :value="old('category', $product->category)" />
            <datalist id="category-options">
                @foreach ($categories as $category)<option value="{{ $category }}">@endforeach
            </datalist>
        </div>

        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand">
                @foreach (['active' => 'Active (visible in shop)', 'inactive' => 'Inactive (hidden)'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $product->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="price" value="Price ($)" />
            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $product->price)" required />
        </div>

        <div>
            <x-input-label for="stock_quantity" value="Units in stock" />
            <x-text-input id="stock_quantity" name="stock_quantity" type="number" step="1" min="0" class="mt-1 block w-full" :value="old('stock_quantity', $product->stock_quantity)" required />
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="description" value="Description" />
            <textarea id="description" name="description" rows="5" maxlength="5000" class="mt-1 block w-full rounded-lg border-gray-300 shadow-xs focus:border-brand focus:ring-brand">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="image" value="Image" />
            @if ($product->image_url)
                <div class="mt-2 flex items-center gap-4">
                    <img src="{{ $product->image_url }}" alt="" class="size-20 rounded-lg border border-default object-cover">
                    <label class="flex items-center gap-2 text-sm text-body">
                        <input type="checkbox" name="remove_image" value="1" class="rounded text-brand focus:ring-brand">
                        Remove current image
                    </label>
                </div>
            @endif
            <input id="image" name="image" type="file" accept="image/*"
                   class="mt-2 block w-full text-sm text-body file:me-4 file:rounded-lg file:border-0 file:bg-brand-softer file:px-4 file:py-2 file:text-sm file:font-semibold file:text-fg-brand-strong hover:file:bg-brand-soft">
            <p class="mt-1 text-xs text-body-subtle">JPG, PNG, WebP or GIF, up to 2 MB. Leave empty to keep the current image.</p>
        </div>
    </div>

    <div class="flex items-center gap-3 border-t border-default pt-5">
        <x-primary-button>{{ $product->exists ? 'Save changes' : 'Create product' }}</x-primary-button>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-body hover:text-heading">Cancel</a>
    </div>
</form>
