<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * 👩 AdminController - El Panel de Administración
 *
 * Este controlador maneja el panel de administración para:
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
     * 📋 MÉTODO: products - Ver lista de productos (Admin)
     *
     * Muestra la tabla completa de productos para administración
     *
     * Ruta: GET /admin/products
     */
public function products()
{
    // Obtener productos paginados (10 por página)
    $productos = Product::with('category')->orderBy('created_at', 'desc')->paginate(10);
    
    // Retornar vista de administración
    return view('admin.products.index', compact('productos'));
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
}
