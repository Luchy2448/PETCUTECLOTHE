<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí definimos todas las RUTAS (direcciones) de nuestra API.
|
| Piensa en esto como las "CALLES Y AVENIDAS" del sitio web.
| Cada ruta es una dirección donde se puede acceder a algo.
|
| Ejemplos:
| - /api/products → Ir al catálogo de productos
| - /api/login    → Ir a la página de inicio de sesión
| - /api/register  → Ir a la página de registro
*/

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (SIN autenticación)
|--------------------------------------------------------------------------
|
| Estas rutas pueden ser accedidas por CUALQUIERA,
| sin necesidad de tener una cuenta o token.
|
| Analogía: Son como el "JARDÍN PÚBLICO" de una casa
| donde puede entrar cualquiera.
*/

// 📝 REGISTRAR nuevo usuario
//
// Permite crear una cuenta nueva en el sistema
// Analogía: Es como el "mostrador de recepción" que registra nuevos visitantes
Route::post('/register', [AuthController::class, 'register']);

// 🔑 INICIAR sesión (login)
//
// Permite que un usuario ya registrado inicie sesión
// Devuelve un token para acceder a rutas protegidas
// Analogía: Es como el "guardia de seguridad" que verifica tu identidad
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (CON autenticación)
|--------------------------------------------------------------------------
|
| Estas rutas SOLO pueden ser accedidas por usuarios AUTENTICADOS
| (que tienen un token válido).
|
| El middleware 'auth:sanctum' verifica que el usuario tenga
| un token válido antes de dejar pasar.
|
| Analogía: Son como el "INTERIOR PRIVADO" de una casa
| donde solo pueden entrar personas con llave (token).
*/

Route::middleware('auth:sanctum')->group(function () {
    // 🚪 CERRAR sesión (logout)
    //
    // Permite al usuario cerrar sesión
    // Revoca el token que estaba usando
    // Analogía: Es como "devolver la llave" cuando sales del edificio
    Route::post('/logout', [AuthController::class, 'logout']);

    // 👤 VER mi usuario (me)
    //
    // Devuelve la información del usuario autenticado
    // Analogía: Es como "mirar mi carnet" para ver mis datos
    Route::get('/user', [AuthController::class, 'me']);

    // 📋 VER todas las categorías
    //
    // Devuelve la lista completa de categorías
    // Requiere: Estar autenticado (tener token)
    // Analogía: Ver todas las carpetas de organización
    Route::apiResource('categories', CategoryController::class);

    // 📋 VER todos los productos (PÚBLICO también)
    //
    // Aunque está dentro del grupo de autenticación,
    // vamos a permitir ver productos sin token también.
    // Requiere: Estar autenticado (tener token) para operar con productos
    Route::apiResource('products', ProductController::class);
});

/*
|--------------------------------------------------------------------------
| RUTAS ESPECIALES (Fuera del middleware)
|--------------------------------------------------------------------------
*/

// 📋 VER todos los productos (PÚBLICO - SIN token)
//
// Esta ruta permite ver el catálogo de productos
// SIN necesidad de iniciar sesión.
// Analogía: Es como el "escaparate de una tienda" donde cualquiera
// puede mirar los productos, aunque no esté registrado.
Route::get('/products', [ProductController::class, 'index']);

// 🔍 VER un producto específico (PÚBLICO - SIN token)
//
// Permite ver los detalles de UN solo producto
// SIN necesidad de iniciar sesión.
// Analogía: Es como "leer la etiqueta" de un producto en el escaparate
Route::get('/products/{id}', [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| NOTAS IMPORTANTES
|--------------------------------------------------------------------------
|
| 1. Route::post() = Método POST para ENVIAR datos (crear, login, etc.)
| 2. Route::get() = Método GET para OBTENER datos (ver, listar)
| 3. Route::put() = Método PUT para ACTUALIZAR datos completos
| 4. Route::delete() = Método DELETE para BORRAR datos
| 5. Route::apiResource() = Crea automáticamente rutas CRUD:
|    - GET    /api/products → index (listar)
|    - POST   /api/products → store (crear)
|    - GET    /api/products/{id} → show (ver uno)
|    - PUT/PATCH /api/products/{id} → update (actualizar)
|    - DELETE /api/products/{id} → destroy (borrar)
|
| 6. middleware('auth:sanctum') = Verifica el token antes de permitir acceso
|    - Si el token es válido → Deja pasar
|    - Si el token es inválido → Devuelve error 401 (Unauthorized)
|    - Si el token expiró → Devuelve error 401 (Unauthorized)
|
| 7. Rutas protegidas requieren el header:
|    Authorization: Bearer 1|token_del_usuario
|
| Ejemplos de uso:
| - Registrar usuario:
|   POST http://localhost:8000/api/register
|   Body: {"name": "Ana", "email": "ana@test.com", "password": "123456", "password_confirmation": "123456"}
|
| - Login:
|   POST http://localhost:8000/api/login
|   Body: {"email": "ana@test.com", "password": "123456"}
|
| - Ver productos (sin login):
|   GET http://localhost:8000/api/products
|
| - Crear producto (con token):
|   POST http://localhost:8000/api/products
|   Headers: Authorization: Bearer 1|token_aleatorio_40_caracteres
|   Body: {"name": "Suéter gatito", "price": 15000, "stock": 10, "size": 3, "category_id": 1, "image_url": "https://..."}
|
*/
