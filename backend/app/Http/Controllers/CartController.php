<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * 🛒 CartController - Gestión del Carrito de Compras
 *
 * Maneja todas las operaciones del carrito:
 * - Agregar productos al carrito
 * - Ver el carrito
 * - Modificar cantidades
 * - Eliminar productos
 * - Vaciar el carrito completo
 * - Calcular el total
 */
class CartController extends Controller
{
    /**
     * 📋 MÉTODO: index - Ver mi carrito (Web)
     *
     * Devuelve la vista del carrito del usuario autenticado
     *
     * Ruta: GET /cart
     * Requiere: Sesión de autenticación web
     */
    public function index()
    {
        try {
            // Obtener los items del carrito con sus productos
            $cartItems = CartItem::with('product')->where('user_id', auth()->id())->get();
            
            // Calcular totales
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
            
            $itemCount = $cartItems->sum('quantity');

            return view('cart.index', compact('cartItems', 'total', 'itemCount'));
            
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error al cargar el carrito: ' . $e->getMessage());
        }
    }

    /**
     * 📋 MÉTODO: apiIndex - Ver mi carrito (API)
     *
     * Devuelve todos los items del carrito del usuario autenticado en formato JSON
     *
     * Ruta: GET /api/cart
     * Requiere: Token de autenticación
     */
    public function apiIndex(): JsonResponse
    {
        try {
            // Obtener los items del carrito con sus productos
            $cartItems = CartItem::with('product')->where('user_id', auth()->id())->get();
            
            // Calcular totales
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
            
            $itemCount = $cartItems->sum('quantity');

            return response()->json([
                'message' => 'Carrito obtenido exitosamente',
                'cart_items' => $cartItems,
                'total' => $total,
                'item_count' => $itemCount,
                'total_formatted' => '$' . number_format($total, 0, ',', '.'),
                'currency' => 'ARS'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el carrito',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ➕ MÉTODO: store - Agregar producto al carrito (Web)
     *
     * Agrega un producto al carrito y redirige al carrito
     *
     * Ruta: POST /cart
     * Requiere: Usuario autenticado
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Validar datos de entrada
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            // Verificar que el producto exista y tenga stock
            $product = Product::find($validated['product_id']);
            if (!$product) {
                return redirect()->back()->with('error', 'Producto no encontrado');
            }

            // Verificar stock disponible
            if ($product->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Stock insuficiente. Solo hay ' . $product->stock . ' unidades disponibles.');
            }

            // Buscar si el producto ya está en el carrito
            $existingItem = CartItem::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingItem) {
                // Si ya existe, actualizar cantidad
                $newQuantity = $existingItem->quantity + $validated['quantity'];
                
                // Verificar stock nuevamente
                if ($product->stock < $newQuantity) {
                    return redirect()->back()->with('error', 'Stock insuficiente. Solo hay ' . $product->stock . ' unidades disponibles. Ya tienes ' . $existingItem->quantity . ' en el carrito.');
                }

                $existingItem->update(['quantity' => $newQuantity]);
                return redirect()->route('cart.index')->with('success', 'Cantidad actualizada exitosamente.');
            } else {
                // Si no existe, crear nuevo item
                CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity']
                ]);
            }

            return redirect()->route('cart.index')->with('success', '¡Producto agregado al carrito exitosamente!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al agregar el producto al carrito: ' . $e->getMessage());
        }
    }

    /**
     * ✏️ MÉTODO: update - Actualizar cantidad de un item
     *
     * Modifica la cantidad de un producto específico en el carrito
     *
     * Ruta: PUT /api/cart/{id}
     * Requiere: Token de autenticación
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            // Buscar el item en el carrito del usuario
            $cartItem = CartItem::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'error' => 'Item no encontrado',
                    'message' => 'Este producto no está en tu carrito'
                ], 404);
            }

            // Verificar stock
            if ($cartItem->product->stock < $validated['quantity']) {
                return response()->json([
                    'error' => 'Stock insuficiente',
                    'message' => 'Solo hay ' . $cartItem->product->stock . ' unidades disponibles',
                    'available_stock' => $cartItem->product->stock
                ], 400);
            }

            // Actualizar cantidad
            $cartItem->update(['quantity' => $validated['quantity']]);

            return response()->json([
                'message' => 'Cantidad actualizada exitosamente',
                'cart_item' => $cartItem->load('product')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar cantidad',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ MÉTODO: destroy - Eliminar item del carrito
     *
     * Elimina un producto específico del carrito
     *
     * Ruta: DELETE /api/cart/{id}
     * Requiere: Token de autenticación
     */
    public function destroy($id): JsonResponse
    {
        try {
            // Buscar el item en el carrito del usuario
            $cartItem = CartItem::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'error' => 'Item no encontrado',
                    'message' => 'Este producto no está en tu carrito'
                ], 404);
            }

            // Eliminar el item
            $cartItem->delete();

            return response()->json([
                'message' => 'Producto eliminado del carrito exitosamente',
                'cart_count' => CartItem::where('user_id', auth()->id())->sum('quantity')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar producto del carrito',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ MÉTODO: clear - Vaciar carrito completo
     *
     * Elimina todos los productos del carrito del usuario
     *
     * Ruta: DELETE /api/cart
     * Requiere: Token de autenticación
     */
    public function clear(): JsonResponse
    {
        try {
            // Eliminar todos los items del carrito del usuario
            $deletedCount = CartItem::where('user_id', auth()->id())->delete();

            return response()->json([
                'message' => 'Carrito vaciado exitosamente',
                'deleted_items' => $deletedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al vaciar el carrito',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 💰 MÉTODO: calculate - Calcular total del carrito
     *
     * Calcula el subtotal y total del carrito del usuario
     *
     * Ruta: POST /api/cart/calculate
     * Requiere: Token de autenticación
     */
    public function calculate(): JsonResponse
    {
        try {
            // Obtener items del carrito
            $cartItems = CartItem::with('product')->where('user_id', auth()->id())->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'El carrito está vacío',
                    'subtotal' => 0,
                    'total' => 0,
                    'item_count' => 0,
                    'currency' => 'ARS'
                ], 200);
            }

            // Calcular subtotal
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Por ahora, el total es igual al subtotal
            // En ETAPA 3 agregaremos costos de envío y descuentos
            $total = $subtotal;
            $itemCount = $cartItems->sum('quantity');

            return response()->json([
                'message' => 'Cálculo del carrito realizado exitosamente',
                'cart_items' => $cartItems,
                'subtotal' => $subtotal,
                'total' => $total,
                'item_count' => $itemCount,
                'subtotal_formatted' => '$' . number_format($subtotal, 0, ',', '.'),
                'total_formatted' => '$' . number_format($total, 0, ',', '.'),
                'currency' => 'ARS'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al calcular el carrito',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
