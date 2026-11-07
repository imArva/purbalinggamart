<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KasirController::class, 'home'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:admin,manajer,kasir')->group(function (): void {
        Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
        Route::get('/kasir/struk/{transaction}', [KasirController::class, 'receipt'])->name('kasir.receipt');
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
        Route::post('/kasir/keranjang', [KasirController::class, 'addToCart'])->name('kasir.cart.add');
        Route::post('/kasir/keranjang/kuantitas', [KasirController::class, 'updateCartQuantity'])->name('kasir.cart.quantity');
        Route::delete('/kasir/keranjang/kosongkan', [KasirController::class, 'clearCart'])->name('kasir.cart.clear');

        Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi');
        Route::get('/transaksi/{transaction}/invoice', [TransactionController::class, 'generateInvoice'])->name('transaksi.invoice');
        Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transaksi.show');
    });

    Route::middleware('role:admin,manajer')->group(function (): void {
        Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/stok', [StockController::class, 'index'])->name('stok');
        Route::post('/stok', [StockController::class, 'update'])->name('stok.update');

        Route::get('/produk', [ProductController::class, 'index'])->name('produk');
        Route::post('/produk', [ProductController::class, 'store'])->name('products.store');
        Route::put('/produk/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/produk/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/akun', [AccountManagerController::class, 'index'])->name('accounts.index');
        Route::post('/akun', [AccountManagerController::class, 'store'])->name('accounts.store');
        Route::put('/akun/{user}', [AccountManagerController::class, 'update'])->name('accounts.update');
        Route::delete('/akun/{user}', [AccountManagerController::class, 'destroy'])->name('accounts.destroy');
    });
});
