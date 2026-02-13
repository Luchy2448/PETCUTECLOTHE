<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web de la aplicación.
| Estas rutas están cargadas por el RouteServiceProvider y todas
| son asignadas al grupo de middleware "web".
|
*/

/*
|--------------------------------------------------------------------------
| 🏠 RUTAS PÚBLICAS (Sin autenticación)
|--------------------------------------------------------------------------
*/

// Página de inicio con productos destacados
Route::get('/', [ProductController::class, 'index'])->name('home');

// 🔍 BÚSQUEDA Y FILTROS
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/filter', [ProductController::class, 'filter'])->name('products.filter');

// 📦 Productos (público)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// 👤 AUTENTICACIÓN (público)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

/*
|--------------------------------------------------------------------------
| 🔐 RUTAS PROTEGIDAS (Requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | 🛒 CARRITO DE COMPRAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    /*
    |--------------------------------------------------------------------------
    | 💳 CHECKOUT Y PAGOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    // Páginas de resultado de pago
    Route::get('/payment/success', [CheckoutController::class, 'success'])->name('payment.success');
    Route::get('/payment/pending', [CheckoutController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/failure', [CheckoutController::class, 'failure'])->name('payment.failure');

    /*
    |--------------------------------------------------------------------------
    | 📋 ÓRDENES DEL USUARIO
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | 🛡️ PANEL ADMINISTRATIVO (Solo administradores)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // 📊 Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // 📦 Gestión de productos
        Route::resource('products', AdminController::class)->names([
            'index' => 'products.index',
            'create' => 'products.create',
            'store' => 'products.store',
            'show' => 'products.show',
            'edit' => 'products.edit',
            'update' => 'products.update',
            'destroy' => 'products.destroy',
        ]);
        
        // 📋 Gestión de órdenes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{order}/ship', [AdminOrderController::class, 'ship'])->name('ship');
            Route::post('/{order}/deliver', [AdminOrderController::class, 'deliver'])->name('deliver');
            Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('cancel');
            Route::get('/stats', [AdminOrderController::class, 'stats'])->name('stats');
        });
        
        // 📈 Estadísticas generales
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    });
});

/*
|--------------------------------------------------------------------------
| 🪝 WEBHOOKS (Públicos pero seguros)
|--------------------------------------------------------------------------
*/

Route::post('/webhook/mercadopago', [PaymentController::class, 'webhook'])->name('webhook.mercadopago');

/*
|--------------------------------------------------------------------------
| 🔄 RUTAS LEGADO (Mantenimiento temporal)
|--------------------------------------------------------------------------
*/

// Mantener compatibilidad temporal con rutas antiguas
Route::get('/cart', [CartController::class, 'index'])->name('cart.legacy');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store.legacy');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update.legacy');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy.legacy');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear.legacy');

/*
|--------------------------------------------------------------------------
| 📄 RUTAS DE INFORMACIÓN (Públicas)
|--------------------------------------------------------------------------
*/

Route::get('/about', function() {
    return view('pages.about');
})->name('about');

Route::get('/contact', function() {
    return view('pages.contact');
})->name('contact');

Route::get('/faq', function() {
    return view('pages.faq');
})->name('faq');

Route::get('/terms', function() {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function() {
    return view('pages.privacy');
})->name('privacy');

/*
|--------------------------------------------------------------------------
| 🔍 RUTAS DE CATEGORÍAS (Públicas)
|--------------------------------------------------------------------------
*/

Route::get('/categories', function() {
    $categorias = \App\Models\Category::withCount('products')->orderBy('name')->get();
    return view('categories.index', compact('categorias'));
})->name('categories.index');

Route::get('/categories/{category:slug}', function($category) {
    $category = \App\Models\Category::where('slug', $category)->with('products')->firstOrFail();
    $productos = $category->products()->with('category')->paginate(12);
    return view('categories.show', compact('category', 'productos'));
})->name('categories.show');

/*
|--------------------------------------------------------------------------
| 🚀 RUTAS DE DESARROLLO (Solo en entorno local)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/dev/info', function() {
        return response()->json([
            'app_name' => config('app.name'),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
            'mail_mailer' => config('mail.default'),
            'mercado_pago_mode' => config('services.mercadopago.mode', 'not_configured'),
        ]);
    })->name('dev.info');
    
    Route::get('/dev/routes', function() {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'method' => implode('|', $route->methods()),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        });
        
        return response()->json($routes);
    })->name('dev.routes');
}