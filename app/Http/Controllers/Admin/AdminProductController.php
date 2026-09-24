<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }
}
