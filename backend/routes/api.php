<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí definimos todas las rutas de la API REST del sistema.
| Incluyen autenticación, productos, carrito, pedidos y pagos.
|
*/

/*
|--------------------------------------------------------------------------
| 🔓 RUTAS PÚBLICAS (Sin autenticación)
|--------------------------------------------------------------------------
|
| Estas rutas pueden ser accedidas sin necesidad de token.
*/

// 📝 Autenticación
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// 📊 Estadísticas públicas
Route::get('/stats/products', function() {
    return response()->json([
        'total_products' => \App\Models\Product::count(),
        'in_stock' => \App\Models\Product::where('stock', '>', 0)->count(),
        'categories' => \App\Models\Category::withCount('products')->get(),
    ]);
});

Route::get('/stats/orders', function() {
    return response()->json([
        'total_orders' => \App\Models\Order::count(),
        'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
        'completed_orders' => \App\Models\Order::where('status', 'delivered')->count(),
        'revenue' => \App\Models\Order::where('status', 'delivered')->sum('total'),
    ]);
});

// 🔍 Búsqueda y filtros
Route::get('/products/search', [SearchController::class, 'suggestions']);
Route::get('/products/filter', [ProductController::class, 'filter']);
Route::get('/products/category/{category}', [ProductController::class, 'byCategory']);

// 📦 Catálogo de productos (público)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// 🏷️ Categorías públicas
Route::get('/categories', function() {
    return response()->json(\App\Models\Category::withCount('products')->get());
});
Route::get('/categories/{category}', function($category) {
    $category = \App\Models\Category::with('products')->findOrFail($category);
    return response()->json($category);
});

// 🪝 Webhooks (públicos pero seguros)
Route::post('/webhook/mercadopago', [PaymentController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| 🔐 RUTAS PROTEGIDAS (Requieren autenticación)
|--------------------------------------------------------------------------
|
| Estas rutas requieren un token válido (Bearer Token).
| El middleware 'auth:sanctum' verifica el token.
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // 👤 Gestión de perfil
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    
    // 🛒 Carrito de compras
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'apiIndex'])->name('index');
        Route::post('/add', [CartController::class, 'apiAdd'])->name('add');
        Route::patch('/{item}', [CartController::class, 'apiUpdate'])->name('update');
        Route::delete('/{item}', [CartController::class, 'apiDestroy'])->name('destroy');
        Route::delete('/', [CartController::class, 'apiClear'])->name('clear');
        Route::post('/calculate', [CartController::class, 'calculate'])->name('calculate');
    });
    
    // 💳 Pagos
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::post('/create', [CheckoutController::class, 'apiCreate'])->name('create');
        Route::get('/{payment}', [PaymentController::class, 'apiShow'])->name('show');
        Route::get('/order/{order}/latest', [PaymentController::class, 'latestByOrder'])->name('latest.by_order');
    });
    
    // 📋 Órdenes del usuario
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'apiIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'apiShow'])->name('show');
        Route::post('/', [OrderController::class, 'apiStore'])->name('store');
        Route::patch('/{order}/cancel', [OrderController::class, 'apiCancel'])->name('cancel');
        Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');
    });
    
    // 🛍️ Gestión de productos (CRUD completo)
    Route::apiResource('products', ProductController::class);
    
    // 🏷️ Gestión de categorías (CRUD completo)
    Route::apiResource('categories', CategoryController::class);
    
    // 📈 Estadísticas del usuario
    Route::get('/stats/user', function() {
        $user = auth()->user();
        return response()->json([
            'total_orders' => \App\Models\Order::where('user_id', $user->id)->count(),
            'total_spent' => \App\Models\Order::where('user_id', $user->id)->where('status', 'delivered')->sum('total'),
            'pending_orders' => \App\Models\Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'cart_items' => \App\Models\CartItem::where('user_id', $user->id)->count(),
            'last_order' => \App\Models\Order::where('user_id', $user->id)->latest()->first(),
        ]);
    });
    
    // 🔄 Historial de búsqueda
    Route::get('/search/history', [SearchController::class, 'history']);
    Route::post('/search/save', [SearchController::class, 'saveSearch']);
});

/*
|--------------------------------------------------------------------------
| 🛡️ RUTAS DE ADMINISTRACIÓN (Solo admin)
|--------------------------------------------------------------------------
|
| Estas rutas requieren autenticación y rol de administrador.
*/

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 👥 Gestión de usuarios
    Route::get('/users', function() {
        return response()->json(\App\Models\User::withCount('orders')->get());
    });
    Route::get('/users/{user}', function($user) {
        $user = \App\Models\User::with(['orders', 'cartItems'])->findOrFail($user);
        return response()->json($user);
    });
    
    // 📊 Estadísticas avanzadas
    Route::get('/stats/dashboard', function() {
        return response()->json([
            'total_users' => \App\Models\User::count(),
            'total_products' => \App\Models\Product::count(),
            'total_orders' => \App\Models\Order::count(),
            'total_revenue' => \App\Models\Order::where('status', 'delivered')->sum('total'),
            'monthly_revenue' => \App\Models\Order::where('status', 'delivered')
                                           ->where('created_at', '>=', now()->subMonth())
                                           ->sum('total'),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'low_stock_products' => \App\Models\Product::where('stock', '<', 5)->count(),
        ]);
    });
    
    // 📋 Gestión completa de órdenes
    Route::get('/orders', function(Request $request) {
        $query = \App\Models\Order::with(['user', 'items.product', 'payments']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    });
    
    Route::patch('/orders/{order}/status', function(Request $request, $order) {
        $order = \App\Models\Order::findOrFail($order);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
        ]);
        
        $order->update([
            'status' => $request->status,
            'tracking_number' => $request->tracking_number,
        ]);
        
        return response()->json(['message' => 'Estado actualizado', 'order' => $order]);
    });
    
    // 📈 Reportes
    Route::get('/reports/sales', function(Request $request) {
        $period = $request->get('period', 'month');
        
        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
        
        $sales = \App\Models\Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
                                ->where('created_at', '>=', $startDate)
                                ->where('status', 'delivered')
                                ->groupBy('date')
                                ->orderBy('date')
                                ->get();
        
        return response()->json($sales);
    });
    
    // 🏆 Productos más vendidos
    Route::get('/reports/top-products', function() {
        $products = \App\Models\OrderItem::select('product_id', 
                'COUNT(*) as orders_count',
                'SUM(quantity) as total_quantity',
                'SUM(quantity * price) as total_revenue')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->where('orders.created_at', '>=', now()->subMonth())
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->with('product')
            ->get();
        
        return response()->json($products);
    });
});

/*
|--------------------------------------------------------------------------
| 🧪 RUTAS DE DESARROLLO (Solo en entorno local)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    // 🔧 Token de prueba
    Route::get('/dev/token', [AuthController::class, 'getTokenForTesting']);
    
    // 📊 Información del sistema
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
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    });
    
    // 🔄 Limpiar caché
    Route::post('/dev/cache/clear', function() {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        
        return response()->json(['message' => 'Caché limpiada correctamente']);
    });
    
    // 📦 Seeders de prueba
    Route::post('/dev/seed', function() {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);
        return response()->json(['message' => 'Datos de prueba creados']);
    });
}

/*
|--------------------------------------------------------------------------
| 📚 DOCUMENTACIÓN DE ENDPOINTS
|--------------------------------------------------------------------------
|
| Formatos de respuesta estándar:
|
| ✅ Respuesta exitosa:
| {
|     "success": true,
|     "data": {...},
|     "message": "Operación completada"
| }
|
| ❌ Respuesta de error:
| {
|     "success": false,
|     "error": "Mensaje de error",
|     "code": "ERROR_CODE"
| }
|
| 📄 Paginación:
| {
|     "data": [...],
|     "current_page": 1,
|     "per_page": 15,
|     "total": 100,
|     "last_page": 7
| }
|
| 🔐 Autenticación:
| Header: Authorization: Bearer {token}
|
| 📝 Ejemplos de uso:
|
| POST /api/auth/register
| {
|     "name": "Juan Pérez",
|     "email": "juan@email.com",
|     "password": "password123",
|     "password_confirmation": "password123"
| }
|
| POST /api/auth/login
| {
|     "email": "juan@email.com",
|     "password": "password123"
| }
|
| GET /api/products
| Headers: Authorization: Bearer {token}
|
| POST /api/cart/add
| Headers: Authorization: Bearer {token}
| {
|     "product_id": 1,
|     "quantity": 2
| }
|
*/