<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * OrderController - Gestión de Pedidos
 *
 * Maneja todas las operaciones de pedidos:
 * - Crear pedidos desde el carrito
 * - Ver historial de pedidos del usuario
 * - Ver detalles de un pedido específico
 * - Cancelar pedidos
 */
class OrderController extends Controller
{
    /**
     * 📋 MÉTODO: index - Ver mis pedidos
     *
     * Devuelve la lista de pedidos del usuario autenticado
     *
     * Ruta: GET /api/orders
     * Requiere: Token de autenticación
     */
    public function index(): JsonResponse
    {
        try {
            $orders = Order::with(['items.product', 'payments'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            // Calcular items count para cada pedido
            $ordersWithCount = $orders->map(function ($order) {
                $order->order_items_count = $order->items->count();
                unset($order->items); // Remover para evitar respuesta anidada
                return $order;
            });

            return response()->json([
                'message' => 'Pedidos obtenidos exitosamente',
                'orders' => $ordersWithCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener pedidos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔍 MÉTODO: show - Ver detalles de pedido
     *
     * Devuelve los detalles completos de un pedido específico
     *
     * Ruta: GET /api/orders/{id}
     * Requiere: Token de autenticación
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['items.product', 'payments'])
                ->where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'error' => 'Pedido no encontrado',
                    'message' => 'El pedido solicitado no existe o no te pertenece'
                ], 404);
            }

            return response()->json([
                'message' => 'Pedido obtenido exitosamente',
                'order' => $order
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🛒 MÉTODO: store - Crear pedido desde carrito
     *
     * Convierte los items del carrito en un pedido
     *
     * Ruta: POST /api/orders
     * Requiere: Token de autenticación
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validar datos de entrada
            $validated = $request->validate([
                'shipping_address' => 'required|string|min:5',
                'phone_number' => 'required|string|min:6'
            ], [
                'shipping_address.required' => 'La dirección de envío es obligatoria',
                'shipping_address.min' => 'La dirección debe tener al menos 5 caracteres',
                'phone_number.required' => 'El número de teléfono es obligatorio',
                'phone_number.min' => 'El teléfono debe tener al menos 6 caracteres'
            ]);

            // Validar que el carrito no esté vacío
            $cartItems = CartItem::with('product')
                ->where('user_id', auth()->id())
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'error' => 'El carrito está vacío',
                    'message' => 'No se puede crear un pedido sin productos en el carrito'
                ], 400);
            }

            // Iniciar transacción para asegurar consistencia
            DB::beginTransaction();

            // Calcular total del pedido
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Crear el pedido
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'phone_number' => $validated['phone_number']
            ]);

            // Mover items del carrito al pedido
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_purchase' => $cartItem->product->price,
                    'size' => (int)($cartItem->size ?? 3), // Valor por defecto como entero
                ]);
            }

            // Vaciar el carrito
            CartItem::where('user_id', auth()->id())->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pedido creado exitosamente',
                'order' => $order->load('items.product')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Error al crear pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ❌ MÉTODO: cancel - Cancelar pedido
     *
     * Cancela un pedido solo si está pendiente
     *
     * Ruta: PUT /api/orders/{id}/cancel
     * Requiere: Token de autenticación
     */
    public function cancel($id): JsonResponse
    {
        try {
            $order = Order::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'error' => 'Pedido no encontrado',
                    'message' => 'El pedido solicitado no existe o no te pertenece'
                ], 404);
            }

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'error' => 'No se puede cancelar',
                    'message' => 'Este pedido ya no puede ser cancelado'
                ], 400);
            }

            $order->markAsCancelled();

            return response()->json([
                'message' => 'Pedido cancelado exitosamente',
                'order' => $order
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cancelar pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}