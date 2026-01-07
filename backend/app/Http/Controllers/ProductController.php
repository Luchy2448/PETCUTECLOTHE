<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 📦 ProductController - El Cerebro de los Productos
 *
 * Este controlador maneja TODO lo relacionado con productos:
 * - Crear nuevos productos
 * - Ver todos los productos
 * - Ver un producto específico
 * - Editar productos existentes
 * - Borrar productos
 *
 * Piensa en esto como el "ENCARGADO DE TIENDA" que:
 * - Agrega nuevas prendas al catálogo
 * - Busca prendas para los clientes
 * - Actualiza información de prendas
 * - Saca prendas que ya no se venden
 */
class ProductController extends Controller
{
    /**
     * 📋 MÉTODO: index - Ver TODOS los productos
     *
     * Este método devuelve la lista completa de productos.
     *
     * Ruta: GET /api/products
     * 
     * Analogía: Es como pedirle al encargado "muéstrame TODO lo que hay en la tienda".
     *
     * @return JsonResponse - Lista de todos los productos
     */
    public function index(): JsonResponse
    {
        // 📦 BUSCAR todos los productos en la base de datos
        //
        // Product::all() hace:
        // 1. Se conecta a la tabla products
        // 2. Trae TODOS los registros
        // 3. Los devuelve como una colección de objetos Product
        $productos = Product::with('category')->get();
        
        // 📋 RETORNAR la lista de productos
        return response()->json($productos, 200);
    }

    /**
     * 🔍 MÉTODO: show - Ver UN producto específico
     *
     * Este método busca y devuelve UN solo producto por su ID.
     *
     * Ruta: GET /api/products/{id}
     * 
     * Analogía: Es como pedirle al encargado "muéstrame esa prenda específica del estante 3".
     *
     * @param int $id - El ID del producto a buscar
     * @return JsonResponse - El producto encontrado o error 404
     */
    public function show($id): JsonResponse
    {
        // 🔍 BUSCAR el producto por su ID
        //
        // Product::find($id) busca en la tabla products
        // un registro donde el id sea igual al que pasamos
        $producto = Product::with('category')->find($id);

        // ❌ VERIFICAR si el producto existe
        if (!$producto) {
            // Si $producto es null, significa que NO existe
            // Devolvemos un error 404 (Not Found = No encontrado)
            return response()->json([
                'error' => 'Producto no encontrado',
                'message' => 'No existe ningún producto con el ID: ' . $id
            ], 404);
        }

        // 📋 RETORNAR el producto encontrado
        return response()->json($producto, 200);
    }

    /**
     * ➕ MÉTODO: store - Crear un NUEVO producto
     *
     * Este método crea un nuevo producto en la base de datos.
     *
     * Ruta: POST /api/products
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al encargado "agrega esta nueva prenda al catálogo".
     *
     * @param Request $request - Los datos del nuevo producto
     * @return JsonResponse - El producto creado o error
     */
    public function store(Request $request): JsonResponse
    {
        // 🛡️ VALIDAR los datos antes de guardar
        //
        // Verificamos que:
        // - Todos los campos obligatorios estén presentes
        // - Los tipos de datos sean correctos
        // - No haya valores inválidos
        $validated = $request->validate([
            'name' => 'required|string|max:255',           // Nombre: obligatorio, texto, máx 255 caracteres
            'description' => 'required|string',                // Descripción: obligatoria, texto
            'price' => 'required|numeric|min:0',            // Precio: obligatorio, número, mínimo 0
            'stock' => 'required|integer|min:0',           // Stock: obligatorio, entero, mínimo 0
            'size' => 'required|integer|between:1,5',     // Talla: obligatoria, entero, entre 1 y 5
            'category_id' => 'required|integer|exists:categories,id', // Categoría: obligatoria, debe existir en tabla categories
            'image_url' => 'required|string|url'          // Imagen: obligatoria, texto, debe ser URL válida
        ], [
            // Mensajes de error personalizados en español
            'name.required' => 'El nombre del producto es obligatorio',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio no puede ser negativo',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'size.required' => 'La talla es obligatoria',
            'size.integer' => 'La talla debe ser un número entero',
            'size.between' => 'La talla debe estar entre 1 y 5',
            'category_id.required' => 'La categoría es obligatoria',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'image_url.required' => 'La imagen es obligatoria',
            'image_url.url' => 'La imagen debe ser una URL válida'
        ]);

        // 📦 CREAR el nuevo producto
        //
        // Product::create($validated) hace:
        // 1. Crea un nuevo objeto Product
        // 2. Llena los campos con los datos validados
        // 3. Guarda el producto en la base de datos
        // 4. Devuelve el producto creado
        $producto = Product::create($validated);

        // 📋 RETORNAR respuesta exitosa
        //
        // Código 201 = Created (Creado exitosamente)
        return response()->json([
            'message' => 'Producto creado exitosamente',
            'product' => $producto
        ], 201);
    }

    /**
     * ✏️ MÉTODO: update - ACTUALIZAR un producto existente
     *
     * Este método modifica la información de un producto ya creado.
     *
     * Ruta: PUT /api/products/{id}
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al encargado "actualiza la información de esta prenda del estante".
     *
     * @param Request $request - Los nuevos datos del producto
     * @param int $id - El ID del producto a actualizar
     * @return JsonResponse - El producto actualizado o error
     */
    public function update(Request $request, $id): JsonResponse
    {
        // 🔍 BUSCAR el producto a actualizar
        $producto = Product::find($id);

        // ❌ VERIFICAR si el producto existe
        if (!$producto) {
            return response()->json([
                'error' => 'Producto no encontrado',
                'message' => 'No existe ningún producto con el ID: ' . $id
            ], 404);
        }

        // 🛡️ VALIDAR los nuevos datos (solo los que se envían)
        //
        // OJO: Usamos sometimes() para que solo valide
        // los campos que VENGAN en el request.
        // Así, el usuario puede actualizar SOLO el nombre,
        // o SOLO el precio, sin tener que enviar TODOS los campos.
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'size' => 'sometimes|integer|between:1,5',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'image_url' => 'sometimes|string|url'
        ], [
            // Mensajes de error personalizados
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio no puede ser negativo',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'size.integer' => 'La talla debe ser un número entero',
            'size.between' => 'La talla debe estar entre 1 y 5',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'image_url.url' => 'La imagen debe ser una URL válida'
        ]);

        // ✏️ ACTUALIZAR el producto
        //
        // $producto->update($validated) hace:
        // 1. Actualiza SOLO los campos que vengan en $validated
        // 2. Guarda los cambios en la base de datos
        // 3. Actualiza automáticamente el campo updated_at
        $producto->update($validated);

        // 🔄 RECARGAR el producto desde la base de datos
        // para traer la relación con categoría
        $producto->load('category');

        // 📋 RETORNAR respuesta exitosa
        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'product' => $producto
        ], 200);
    }

    /**
     * 🗑️ MÉTODO: destroy - ELIMINAR un producto
     *
     * Este método borra un producto de la base de datos.
     *
     * Ruta: DELETE /api/products/{id}
     * Requiere: Token de autenticación (middleware auth:sanctum)
     * 
     * Analogía: Es como decirle al encargado "saca esta prenda del catálogo, ya no se vende".
     *
     * @param int $id - El ID del producto a borrar
     * @return JsonResponse - Confirmación o error
     */
    public function destroy($id): JsonResponse
    {
        // 🔍 BUSCAR el producto a borrar
        $producto = Product::find($id);

        // ❌ VERIFICAR si el producto existe
        if (!$producto) {
            return response()->json([
                'error' => 'Producto no encontrado',
                'message' => 'No existe ningún producto con el ID: ' . $id
            ], 404);
        }

        // 🗑️ BORRAR el producto
        //
        // $producto->delete() hace:
        // 1. Borra el registro de la base de datos
        // 2. Si hay registros relacionados (con onDelete('cascade')),
        //    también los borra automáticamente
        $producto->delete();

        // 📋 RETORNAR respuesta exitosa
        return response()->json([
            'message' => 'Producto eliminado exitosamente',
            'id' => $id
        ], 200);
    }
}
