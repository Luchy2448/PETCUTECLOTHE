<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * 📦 ProductController - Controlador público de productos
 *
 * Muestra productos a los usuarios con búsqueda y filtros
 */
class ProductController extends Controller
{
    /**
     * 📋 MÉTODO: index - Lista de productos con filtros (pública)
     *
     * Muestra productos con filtros aplicados
     *
     * Ruta: GET /products
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Aplicar filtros si existen
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->size) {
            $query->where('size', $request->size);
        }
        
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }
        
        // Ordenamiento
        $sortBy = $request->sort ?? 'created_at_desc';
        $query = $this->applySorting($query, $sortBy);
        
        // Paginación
        $productos = $query->paginate(12);
        
        // Obtener categorías para filtros
        $categorias = Category::orderBy('name')->get();
        
        return view('products.index', compact('productos', 'categorias'));
    }

    /**
     * 🔍 MÉTODO: search - Búsqueda de productos
     *
     * Ruta: GET /search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $sortBy = $request->get('sort', 'relevance');
        
        $productos = Product::search($query)
            ->with('category')
            ->sort($sortBy)
            ->paginate(12);
            
        $categorias = Category::orderBy('name')->get();
        
        return view('products.search', compact('productos', 'query', 'categorias'));
    }

    /**
     * 🏷️ MÉTODO: byCategory - Productos por categoría
     *
     * Ruta: GET /category/{category}
     */
    public function byCategory(Category $category)
    {
        $productos = Product::where('category_id', $category->id)
                           ->with('category')
                           ->paginate(12);
        
        $categorias = Category::orderBy('name')->get();
        
        return view('products.category', compact('productos', 'category', 'categorias'));
    }

    /**
     * 🔧 MÉTODO: filter - Filtros avanzados
     *
     * Ruta: GET /filter
     */
    public function filter(Request $request)
    {
        $query = Product::with('category');
        
        // Aplicar todos los filtros
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->size) {
            $query->where('size', $request->size);
        }
        
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }
        
        if ($request->brand) {
            $query->where('brand', 'LIKE', "%{$request->brand}%");
        }
        
        // Ordenamiento
        $sortBy = $request->sort ?? 'name_asc';
        $query = $this->applySorting($query, $sortBy);
        
        $productos = $query->paginate(12);
        $categorias = Category::orderBy('name')->get();
        
        return view('products.filtered', compact('productos', 'categorias'));
    }

    /**
     * 📋 MÉTODO: show - Detalle de producto (público)
     *
     * Ruta: GET /products/{id}
     */
    public function show($id)
    {
        $producto = Product::with('category')->find($id);

        if (!$producto) {
            return redirect()->route('products.index')
                ->with('error', 'Producto no encontrado');
        }

        // Productos relacionados (misma categoría)
        $relacionados = Product::where('category_id', $producto->category_id)
                               ->where('id', '!=', $producto->id)
                               ->with('category')
                               ->limit(4)
                               ->get();

        return view('products.show', compact('producto', 'relacionados'));
    }

    /**
     * 🔧 Método helper para aplicar ordenamiento
     */
    private function applySorting($query, $sortBy)
    {
        return match ($sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}
