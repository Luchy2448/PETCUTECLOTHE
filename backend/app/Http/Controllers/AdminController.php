<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * 👩 AdminController - El Panel de Administración
 *
 * Este controlador maneja el panel de administración para:
 * - Dashboard con estadísticas
 * - Ver lista de productos
 * - Ver formulario de creación
 * - Crear nuevos productos
 * - Ver formulario de edición
 * - Actualizar productos existentes
 * - Ver detalles de producto
 * - Eliminar productos
 */
class AdminController extends Controller
{
    /**
     * 📊 Dashboard - Panel principal de administración
     *
     * Muestra estadísticas generales y pedidos recientes
     *
     * Ruta: GET /admin/dashboard
     */
    public function dashboard()
    {
        // Estadísticas de pedidos
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
        ];

        // Pedidos recientes
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Estadísticas de productos
        $productStats = [
            'total' => Product::count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 5)->count(),
        ];

        return view('admin.dashboard.index', compact('stats', 'recentOrders', 'productStats'));
    }

    /**
     * 📋 Index - Lista de productos (para Route::resource)
     */
    public function index()
    {
        return $this->productsList();
    }

    /**
     * 🏠 Admin Home - Punto de entrada principal del admin
     *
     * Muestra diferentes vistas según la ruta accedida
     */
    public function adminHome(Request $request)
    {
        $path = $request->path();
        
        if (str_contains($path, 'productos')) {
            return $this->productsList();
        }
        if (str_contains($path, 'categories') || str_contains($path, 'categorias')) {
            return $this->categoriesList();
        }
        if (str_contains($path, 'orders') || str_contains($path, 'pedidos')) {
            return $this->ordersList();
        }
        if (str_contains($path, 'users') || str_contains($path, 'usuarios')) {
            return $this->usersList();
        }
        
        return $this->dashboard();
    }
    
    /**
     * 📋 productsList - Ver lista de productos (Admin)
     */
    private function productsList()
    {
        $productos = Product::with('category')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('productos'));
    }
    
    /**
     * 📋 MÉTODO: categoriesList - Ver lista de categorías (Admin)
     */
    private function categoriesList()
    {
        $categorias = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categorias'));
    }
    
    /**
     * 📋 MÉTODO: ordersList - Ver lista de pedidos (Admin)
     */
    private function ordersList()
    {
        $orders = \App\Models\Order::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }
    
    /**
     * 📋 MÉTODO: usersList - Ver lista de usuarios (Admin)
     */
    private function usersList()
    {
        $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * 📋 MÉTODO: create - Mostrar formulario de creación
     *
     * Muestra el formulario vacío para crear un nuevo producto
     *
     * Ruta: GET /admin/products/create
     */
    public function create()
    {
        // Obtener todas las categorías para el select
        $categorias = Category::all();
        
        // Retornar vista de creación con categorías
        return view('admin.products.create', compact('categorias'));
    }

    /**
     * 📋 MÉTODO: store - Crear nuevo producto
     *
     * Procesa el formulario de creación y guarda en la base de datos
     *
     * Ruta: POST /admin/products (store)
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'size' => 'required|integer|between:1,5',
            'category_id' => 'required|integer|exists:categories,id',
            'image_url' => 'required|string|url'
        ]);

        // Crear el producto
        $producto = Product::create($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente: ' . $producto->name);
    }

    /**
     * 📋 MÉTODO: edit - Mostrar formulario de edición
     *
     * Muestra el formulario con los datos del producto existente
     *
     * Ruta: GET /admin/products/{id}/edit
     */
    public function edit($id)
    {
        // Buscar el producto
        $producto = Product::with('category')->find($id);

        // Verificar que exista
        if (!$producto) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Obtener categorías
        $categorias = Category::all();

        // Retornar vista de edición
        return view('admin.products.edit', compact('producto', 'categorias'));
    }

    /**
     * 📋 MÉTODO: update - Actualizar producto existente
     *
     * Procesa el formulario de edición y actualiza el producto
     *
     * Ruta: POST /admin/products (store)
     */
    public function update(Request $request, $id)
    {
        // Buscar el producto
        $producto = Product::find($id);

        // Verificar que exista
        if (!$producto) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Validar datos
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'size' => 'sometimes|integer|between:1,5',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'image_url' => 'sometimes|string|url'
        ]);

        // Actualizar el producto
        $producto->update($validated);

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado exitosamente: ' . $producto->name);
    }

    /**
     * 📋 MÉTODO: destroy - Eliminar producto
     *
     * Elimina un producto de la base de datos
     *
     * Ruta: POST /admin/products/{id}/destroy (con confirmación JS)
     * Ruta: DELETE /admin/products/{id} (sin confirmación)
     */
    public function destroy($id)
    {
        // Buscar el producto
        $producto = Product::find($id);

        // Verificar que exista
        if (!$producto) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Eliminar primero los order_items relacionados
        \App\Models\OrderItem::where('product_id', $id)->delete();

        // Eliminar el producto
        $producto->delete();

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado exitosamente: ' . $producto->name);
    }

    /**
     * 📋 MÉTODO: show - Ver detalles de producto
     *
     * Muestra los detalles completos de un producto específico
     *
     * Ruta: GET /admin/products/{id}
     */
    public function show($id)
    {
        // Buscar el producto con su categoría
        $producto = Product::with('category')->find($id);

        // Verificar que exista
        if (!$producto) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Retornar vista de detalles
        return view('admin.products.show', compact('producto'));
    }

    /**
     * Hacer admin a un usuario
     */
    public function makeAdmin(\App\Models\User $user)
    {
        $user->is_admin = true;
        $user->save();

        return redirect()->route('admin.users.list')
            ->with('success', 'Usuario ' . $user->name . ' ahora es administrador');
    }
}
