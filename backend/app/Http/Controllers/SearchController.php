<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * 🔍 SearchController - Controlador de búsqueda
 *
 * Maneja las búsquedas de productos y resultados avanzados
 */
class SearchController extends Controller
{
    /**
     * 🔍 Mostrar resultados de búsqueda
     *
     * Ruta: GET /search
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $sortBy = $request->get('sort', 'relevance');
        $categoryId = $request->get('category_id');
        
        // Construir consulta de productos
        $productsQuery = Product::search($query);
        
        // Aplicar filtro de categoría si existe
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        
        // Aplicar ordenamiento
        $productsQuery->sort($sortBy);
        
        // Obtener productos con paginación
        $products = $productsQuery->with('category')->paginate(12);
        
        // Obtener categorías para filtros
        $categories = Category::orderBy('name')->get();
        
        // Preparar datos para la vista
        $searchData = [
            'query' => $query,
            'sort' => $sortBy,
            'category_id' => $categoryId,
            'results_count' => $products->total(),
        ];
        
        return view('products.search', compact('products', 'categories', 'searchData'));
    }
    
    /**
     * 📊 Búsqueda AJAX para autocompletar
     *
     * Ruta: GET /search/suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $products = Product::search($query)
            ->with('category')
            ->limit(8)
            ->get(['id', 'name', 'price', 'image_url', 'category_id']);
        
        $suggestions = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->precioFormateado,
                'image' => $product->imagenUrl,
                'category' => $product->category->name ?? '',
                'url' => route('products.show', $product->id),
            ];
        });
        
        return response()->json($suggestions);
    }
    
    /**
     * 📈 Estadísticas de búsqueda (para admin)
     *
     * Ruta: GET /search/stats
     */
    public function stats(Request $request)
    {
        // Esta función podría registrar búsquedas populares
        // Por ahora es un placeholder para futuras implementaciones
        
        $recentSearches = [
            'sweater para perro',
            'vestido gatito',
            'corbata elegante',
            'camiseta casual',
        ];
        
        $popularProducts = Product::with('category')
            ->inRandomOrder()
            ->limit(5)
            ->get();
        
        return response()->json([
            'recent_searches' => $recentSearches,
            'popular_products' => $popularProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->precioFormateado,
                    'category' => $product->category->name ?? '',
                    'views' => rand(10, 1000), // Placeholder
                ];
            }),
        ]);
    }
}