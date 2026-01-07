<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 📁 CategoryController - El Organizador de Categorías
 *
 * Este controlador maneja todo lo relacionado con categorías:
 * - Crear nuevas categorías
 * - Ver todas las categorías
 * - Ver una categoría específica
 * - Editar categorías existentes
 * - Borrar categorías
 *
 * Piensa en esto como el "ENCARGADO DE ORGANIZAR" que:
 * - Crea nuevas carpetas para organizar las prendas
 * - Busca carpetas para mostrarlas
 * - Modifica nombres de carpetas
 * - Borra carpetas que ya no se necesitan
 */
class CategoryController extends Controller
{
    /**
     * 📋 MÉTODO: index - Ver TODAS las categorías
     *
     * Este método devuelve la lista completa de categorías.
     *
     * Ruta: GET /api/categories
     * 
     * Analogía: Es como pedirle al organizador "muéstrame TODAS las carpetas que hay".
     *
     * @return JsonResponse - Lista de todas las categorías
     */
    public function index(): JsonResponse
    {
        // 📁 BUSCAR todas las categorías en la base de datos
        //
        // Category::all() hace:
        // 1. Se conecta a la tabla categories
        // 2. Trae TODOS los registros
        // 3. Los devuelve como una colección de objetos Category
        $categorias = Category::all();
        
        // 📋 RETORNAR la lista de categorías
        return response()->json($categorias, 200);
    }

    /**
     * 🔍 MÉTODO: show - Ver UNA categoría específica
     *
     * Este método busca y devuelve UNA sola categoría por su ID.
     *
     * Ruta: GET /api/categories/{id}
     * 
     * Analogía: Es como pedirle al organizador "muéstrame ESA carpeta específica del estante 3".
     *
     * @param int $id - El ID de la categoría a buscar
     * @return JsonResponse - La categoría encontrada o error 404
     */
    public function show($id): JsonResponse
    {
        // 🔍 BUSCAR la categoría por su ID
        //
        // Category::find($id) busca en la tabla categories
        // un registro donde el id sea igual al que pasamos
        $categoria = Category::find($id);

        // ❌ VERIFICAR si la categoría existe
        if (!$categoria) {
            // Si $categoria es null, significa que NO existe
            // Devolvemos un error 404 (Not Found = No encontrado)
            return response()->json([
                'error' => 'Categoría no encontrada',
                'message' => 'No existe ninguna categoría con el ID: ' . $id
            ], 404);
        }

        // 📋 RETORNAR la categoría encontrada
        return response()->json($categoria, 200);
    }

    /**
     * ➕ MÉTODO: store - Crear una NUEVA categoría
     *
     * Este método crea una nueva categoría en la base de datos.
     *
     * Ruta: POST /api/categories
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al organizador "crea una NUEVA carpeta llamada 'Casual'".
     *
     * @param Request $request - Los datos de la nueva categoría
     * @return JsonResponse - La categoría creada o error
     */
    public function store(Request $request): JsonResponse
    {
        // 🛡️ VALIDAR los datos antes de guardar
        //
        // Verificamos que:
        // - El nombre no esté vacío
        // - El nombre sea texto
        // - El nombre no tenga más de 255 caracteres
        //
        // Si algo falla, Laravel devuelve un error automático
        $validated = $request->validate([
            'name' => 'required|string|max:255'  // Nombre: obligatorio, texto, máx 255 caracteres
        ], [
            // Mensajes de error personalizados en español
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede tener más de 255 caracteres'
        ]);

        // 📁 CREAR la nueva categoría
        //
        // Category::create($validated) hace:
        // 1. Crea un nuevo objeto Category
        // 2. Llena los campos con los datos validados
        // 3. Guarda la categoría en la base de datos
        // 4. Devuelve la categoría creada
        $categoria = Category::create($validated);

        // 📋 RETORNAR respuesta exitosa
        //
        // Código 201 = Created (Creado exitosamente)
        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'category' => $categoria
        ], 201);
    }

    /**
     * ✏️ MÉTODO: update - ACTUALIZAR una categoría existente
     *
     * Este método modifica el nombre de una categoría ya creada.
     *
     * Ruta: PUT /api/categories/{id}
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al organizador "cambia el nombre de esta carpeta de 'Casual' a 'Casual y Cómodo'".
     *
     * @param Request $request - El nuevo nombre de la categoría
     * @param int $id - El ID de la categoría a actualizar
     * @return JsonResponse - La categoría actualizada o error
     */
    public function update(Request $request, $id): JsonResponse
    {
        // 🔍 BUSCAR la categoría a actualizar
        $categoria = Category::find($id);

        // ❌ VERIFICAR si la categoría existe
        if (!$categoria) {
            return response()->json([
                'error' => 'Categoría no encontrada',
                'message' => 'No existe ninguna categoría con el ID: ' . $id
            ], 404);
        }

        // 🛡️ VALIDAR los nuevos datos (solo los que se envían)
        //
        // OJO: Usamos sometimes() para que solo valide
        // el nombre si VENGA en el request.
        // Así, si el usuario NO envía un nombre, no hay error.
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255'
        ], [
            // Mensajes de error personalizados
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede tener más de 255 caracteres'
        ]);

        // ✏️ ACTUALIZAR la categoría
        //
        // $categoria->update($validated) hace:
        // 1. Actualiza SOLO los campos que vengan en $validated
        // 2. Guarda los cambios en la base de datos
        // 3. Actualiza automáticamente el campo updated_at
        $categoria->update($validated);

        // 📋 RETORNAR respuesta exitosa
        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'category' => $categoria
        ], 200);
    }

    /**
     * 🗑️ MÉTODO: destroy - ELIMINAR una categoría
     *
     * Este método borra una categoría de la base de datos.
     *
     * Ruta: DELETE /api/categories/{id}
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al organizador "borra esta carpeta completa".
     *
     * IMPORTANTE: Si borras una categoría que tiene productos,
     * todos sus productos se borran también (cascade).
     *
     * @param int $id - El ID de la categoría a borrar
     * @return JsonResponse - Confirmación o error
     */
    public function destroy($id): JsonResponse
    {
        // 🔍 BUSCAR la categoría a borrar
        $categoria = Category::find($id);

        // ❌ VERIFICAR si la categoría existe
        if (!$categoria) {
            return response()->json([
                'error' => 'Categoría no encontrada',
                'message' => 'No existe ninguna categoría con el ID: ' . $id
            ], 404);
        }

        // 🗑️ BORRAR la categoría
        //
        // $categoria->delete() hace:
        // 1. Borra el registro de la base de datos
        // 2. Si hay productos relacionados (con onDelete('cascade')),
        //    también los borra automáticamente
        $categoria->delete();

        // 📋 RETORNAR respuesta exitosa
        return response()->json([
            'message' => 'Categoría eliminada exitosamente',
            'id' => $id
        ], 200);
    }
}
