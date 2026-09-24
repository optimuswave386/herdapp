<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductController;

use App\Http\Controllers\ShoppingcartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChartController;

use App\Models\shoppingcart;
use App\Models\Product;
use App\Models\Category;
use App\Models\Posts;
use App\Models\Photos;
use App\Models\User;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function ()      {   return view('welcome');   });
Route::get('/about', function () {   return view('about');   });

Route::middleware('guest')->group(function () {
    Route::get('login/google', [UserController::class, 'redirectToProvider'])->name('social.login');
    Route::get('login/google/callback', [UserController::class, 'handleProviderCallback'])->name('social.callback');
});

Route::get('/shoppingcart', [ShoppingcartController::class, 'index'])->name('shoppingcart.index');
Route::middleware('auth')->group(function () {
    Route::get('/shoppingcart/show', [ShoppingcartController::class, 'show'])->name('shoppingcart.show');
    Route::get('/shoppingcart/count', [ShoppingcartController::class, 'count'])->name('shoppingcart.count');
    Route::post('/shoppingcart/add/{id}', [ShoppingcartController::class, 'add'])->name('shoppingcart.add');
    Route::post('/shoppingcart/update/{id}', [ShoppingcartController::class, 'update'])->name('shoppingcart.update');
    Route::post('/shoppingcart/remove/{id}', [ShoppingcartController::class, 'remove'])->name('shoppingcart.remove');
    Route::post('/shoppingcart/clear', [ShoppingcartController::class, 'clear'])->name('shoppingcart.clear');
    Route::get('/shoppingcart/checkout', [ShoppingcartController::class, 'checkout'])->name('shoppingcart.checkout');
    Route::post('/shoppingcart/checkout', [OrderController::class, 'store'])->name('shoppingcart.place');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::middleware('auth')->group(function () {
    Route::get('/products/view/{product}', [ProductController::class, 'show'])->name('products.show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/products', [App\Http\Controllers\Admin\AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/edit/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/delete/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/orders', [App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}', [App\Http\Controllers\Admin\AdminOrderController::class, 'update'])->name('admin.orders.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/posts/edit/{id}', [PostsController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/edit/{id}', [PostsController::class, 'update'])->name('posts.update');
    Route::get('/posts/create', [PostsController::class, 'create'])->name('posts.create');
    Route::post('/posts/create', [PostsController::class, 'store'])->name('posts.store');
    Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');
    Route::get('/posts/delete/{id}', [PostsController::class, 'destroy'])->name('posts.delete');
    Route::get('/posts/view/{id}', [PostsController::class, 'show'])->name('posts.view');
    Route::get('/posts/category/{categoryId}', [PostsController::class, 'showByCategory'])->name('posts.showByCategory');
    Route::get('/posts/user/{userId}', [PostsController::class, 'showByUser'])->name('posts.showByUser');
});

Route::middleware('auth')->group(function () {  
    Route::get('/portal', [UserController::class, 'portal'])->name('user.portal');
    Route::get('/@{username}/photos', [UserController::class, 'photos'])->name('user.photos');
    Route::get('/@{username}', [UserController::class, 'index'])->name('user.index');
    Route::get('/@{username}/followers', [UserController::class, 'followers'])->name('user.followers');
    Route::get('/@{username}/settings', [UserController::class, 'settings'])->name('user.settings');
    Route::post('/toggle-follow/{userId}', [UserController::class, 'toggleFollow'])->name('user.toggleFollow');
});

require __DIR__.'/auth.php';
