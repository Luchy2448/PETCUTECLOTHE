<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (Sin autenticación)
|--------------------------------------------------------------------------
*/

// Página de inicio
Route::get('/', function() {
    $productos = \App\Models\Product::with('category')->orderBy('created_at', 'desc')->paginate(12);
    return view('home', compact('productos'));
})->name('home');

// Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

// Productos - Público (cualquiera puede ver)
Route::get('/products', function() {
    $productos = \App\Models\Product::with('category')->orderBy('created_at', 'desc')->get();
    return view('products.index', compact('productos'));
})->name('products.index');

// 🛒 RUTAS DEL CARRITO (Web)
Route::middleware(['auth'])->group(function () {
    // 📋 Ver mi carrito
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    
    // ➕ Agregar producto al carrito
    Route::post('/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    
    // ✏️ Actualizar cantidad
    Route::put('/cart/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    
    // 🗑️ Eliminar item
    Route::delete('/cart/{id}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
    
    // 🧹 Vaciar carrito completo
    Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    
    // 💳 Checkout (ETAPA 3 - Próximamente)
    Route::get('/checkout', function() {
        return view('checkout.coming-soon');
    })->name('checkout');
});

Route::get('/products/{id}', function($id) {
    $producto = \App\Models\Product::with('category')->find($id);
    if (!$producto) {
        return redirect()->route('products.index')->with('error', 'Producto no encontrado');
    }
    return view('products.show', compact('producto'));
})->name('products.show');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Panel de administración de productos
    Route::prefix('admin')->group(function () {
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products.index');
        Route::get('/products/create', [AdminController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{id}/edit', [AdminController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{id}', [AdminController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [AdminController::class, 'destroy'])->name('admin.products.destroy');
        Route::get('/products/{id}', [AdminController::class, 'show'])->name('admin.products.show');
    });
});
