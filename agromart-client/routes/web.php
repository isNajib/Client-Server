<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\AuthController;

// Kategori dan Produk
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/products/{categoryId}', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/show/{id}', [ProductController::class, 'show'])->name('product.show');
// Keranjang Belanja
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

// Pesanan
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'create'])->name('orders.create');

Route::get('data', [DataController::class, 'index'])->name('data.index');
Route::post('data', [DataController::class, 'store'])->name('products.store');
Route::get('data/{id}', [DataController::class, 'edit'])->name('data.edit');
Route::put('data/{id}', [DataController::class, 'update'])->name('data.update');
Route::delete('data/{id}', [DataController::class, 'destroy'])->name('data.delete');

// routes/web.php (client-side)
Route::post('/reviews', [ProductController::class, 'storeReview'])->name('reviews.store');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');
