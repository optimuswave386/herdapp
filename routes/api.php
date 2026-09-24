<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\User_has_followers;
use App\Models\shoppingcart;
use App\Models\Product;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ShoppingcartController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () { 
    // route is /api/user
    // Returns the authenticated user's information
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });
});

Route::get('/products', [ProductController::class, 'index']);
Route::post('/shoppingcart/store', [ShoppingcartController::class, 'store'])->name('cart.store');
Route::post('/shoppingcart/add', [ShoppingcartController::class, 'add'])->name('cart.add');
Route::post('/shoppingcart/count', [ShoppingcartController::class, 'count'])->name('cart.count');


// route is /api/status
// Returns JSON status of the API service
Route::get('/status', function () {
    return response()->json([
        'service' => 'Herd API',
        'status' => 'operational',
        'uptime' => '99.99%'
    ]);
})->middleware('auth:sanctum');
