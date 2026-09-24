<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * The public shop: only active products.
     */
    public function index()
    {
        $products = Product::where('status', 'active')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Product detail page. Inactive products are only visible to admins.
     */
    public function show(Product $product)
    {
        abort_unless($product->isActive() || auth()->user()?->is_admin, 404);

        return view('products.view', compact('product'));
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('products.create', [
            'product' => new Product(['status' => 'active', 'stock_quantity' => 0]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        $data = $request->safe()->except(['image', 'remove_image']);
        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url($request->file('image')->store('products', 'public'));
        }

        $product = Product::create($data);

        return redirect()->route('admin.products.index')->with('success', "“{$product->name}” was created.");
    }

    public function edit(Product $product)
    {
        Gate::authorize('update', $product);

        return view('products.edit', ['product' => $product, 'categories' => $this->categories()]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);

        $data = $request->safe()->except(['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($product);
            $data['image_url'] = Storage::url($request->file('image')->store('products', 'public'));
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredImage($product);
            $data['image_url'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', "“{$product->name}” was updated.");
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        // Past orders keep their own copy of the name and price, so this is safe.
        $this->deleteStoredImage($product);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "“{$product->name}” was deleted.");
    }

    private function categories(): array
    {
        return Product::whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all();
    }

    private function deleteStoredImage(Product $product): void
    {
        if ($path = $product->storedImagePath()) {
            Storage::disk('public')->delete($path);
        }
    }
}
