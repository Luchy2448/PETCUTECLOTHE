<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Mostrar lista de categorías
     */
    public function index()
    {
        $categorias = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categorias'));
    }

    /**
     * Crear nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $categoria = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.categories.list')
            ->with('success', 'Categoría creada exitosamente');
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, $id)
    {
        $categoria = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $categoria->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.categories.list')
            ->with('success', 'Categoría actualizada');
    }

    /**
     * Eliminar categoría
     */
    public function destroy($id)
    {
        $categoria = Category::findOrFail($id);

        if ($categoria->products_count > 0) {
            return redirect()->route('admin.categories.list')
                ->with('error', 'No se puede eliminar una categoría con productos');
        }

        $categoria->delete();

        return redirect()->route('admin.categories.list')
            ->with('success', 'Categoría eliminada');
    }
}
