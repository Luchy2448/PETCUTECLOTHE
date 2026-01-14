<?php

namespace App\Http\Controllers;

use App\Models\Product;

/**
 * 📦 ProductController - Controlador público de productos
 *
 * Muestra productos a los usuarios (sin autenticación requerida)
 */
class ProductController extends Controller
{
    /**
     * 📋 MÉTODO: index - Lista de productos (pública)
     *
     * Muestra todos los productos disponibles
     *
     * Ruta: GET /products
     */
    public function index()
    {
        // Obtener todos los productos con su categoría
        $productos = Product::with('category')->orderBy('created_at', 'desc')->get();

        // Retornar vista de productos
        return view('products.index', compact('productos'));
    }

    /**
     * 📋 MÉTODO: show - Detalle de producto (público)
     *
     * Muestra los detalles de un producto específico
     *
     * Ruta: GET /products/{id}
     */
    public function show($id)
    {
        // Buscar el producto con su categoría
        $producto = Product::with('category')->find($id);

        // Verificar que exista
        if (!$producto) {
            return redirect()->route('products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Retornar vista de detalles
        return view('products.show', compact('producto'));
    }
}
