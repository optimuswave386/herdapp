<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\shoppingcart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShoppingcartController extends Controller
{

    public $cartitems = [];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = $this->cartitems;
        //dd($cart);
        //$cartItems = shoppingcart::all();
        //$cartItems = session()->get('cart', []);
        //$cartItems = session('cart', []);
        //$cartItems = Session::get('cart', []);
        //dd($cartItems);
        return view('shoppingcart.index', compact('cart'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->input('cart'));
        $cart = $request->input('cart');
        $this->cartitems = array_merge([], $cart);
        
        return response()->json(['message' => 'Cart data stored successfully', 'cart' => $this->cartitems]);
    }

    /**
     * Display the specified resource.
     */
    public function show(shoppingcart $shoppingcart)
    {
        //
        return response()->json(['cart' => $this->cartitems]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(shoppingcart $shoppingcart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, shoppingcart $shoppingcart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(shoppingcart $shoppingcart)
    {
        // clear the shopping cart session
        session()->forget('cart');
        return response()->json(['message' => 'Shopping cart cleared successfully']);
    }


    /**
     * Add the product to shopping cart session object.
     */
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);
        $userId = $request->input('user_id');
        $quantity = $request->input('quantity', 1);
        
        $cart = session()->get('cart', []);
        if(isset($cart[$productId])) { 
            //$cart[$productId]['quantity']++;
            $cart[$productId]['quantity'] += $quantity;
            $cart[$productId]['price'] = $product->price * $cart[$productId]['quantity'];
            //$cart[$productId] += $quantity;
            // Update other details if necessary
        } else { 
            $cart[$productId] = [
                "product_id" => $productId,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                // Add other details you need
            ];
        }
        session()->put('cart', $cart);
        //session()->keep('cart');
        //session()->save();

        // prevent session drop on page refresh
        return response()->json(['message' => 'Product added to cart', 'cart' => $cart]);
        //return response()->json(['message' => 'Product added to cart successfully!', 'product' => $product, 'user_id' => $userId, 'quantity' => $quantity]);
        //return response()->json(['message' => 'Product added to cart successfully!']);       
    }

    /**
     * Count the products in the shopping cart session object.
     */
    public function count(Request $request)
    {
        //$cart = $this->cartitems;
        $cart = $request->input('cart');
        //$cart = session()->get('cart', []);
        //$count = array_sum($cart);
        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $count]);
    }

    public function checkout()
    {
        //
        return view('checkout.index');
    }

}
