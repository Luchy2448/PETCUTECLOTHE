<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * 💳 CheckoutController - Controlador del proceso de checkout
 *
 * Maneja el proceso completo de compra desde el carrito hasta el pago
 */
class CheckoutController extends Controller
{
    /**
     * 🛒 Mostrar página de checkout
     *
     * Ruta: GET /checkout
     */
    public function index()
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para continuar con el checkout');
            }

            $user = Auth::user();
            
            // Verificar que el usuario tenga ID
            if (!$user || !$user->id) {
                return redirect()->route('cart.index')
                    ->with('error', 'Error de autenticación. Por favor, inicia sesión nuevamente.');
            }

            // Obtener items del carrito con más validación
            $cartItems = CartItem::where('user_id', $user->id)
                                ->with('product')
                                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Tu carrito está vacío');
            }

            // Validar que cada item tenga producto válido
            foreach ($cartItems as $item) {
                if (!$item->product) {
                    return redirect()->route('cart.index')
                        ->with('error', 'Hay productos en tu carrito que ya no están disponibles. Por favor, revisa tu carrito.');
                }
            }

            // Calcular totales de forma más segura
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if ($item->product && $item->quantity > 0 && $item->product->price > 0) {
                    $subtotal += $item->quantity * $item->product->price;
                }
            }
            
            // Aquí podríamos agregar costo de envío, impuestos, etc.
            $envio = $subtotal > 50000 ? 0 : 3000; // Envío gratis sobre $50.000
            $total = $subtotal + $envio;

            return view('checkout.index', compact('cartItems', 'subtotal', 'envio', 'total', 'user'));

        } catch (\Exception $e) {
            Log::error('Error en checkout.index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Ocurrió un error al cargar el checkout. Por favor, inténtalo nuevamente.');
        }
    }

    /**
     * 🔄 Procesar el checkout
     *
     * Ruta: POST /checkout/process
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return back()->with('error', 'Debes estar autenticado para procesar el pedido');
            }

            $user = Auth::user();
            
            if (!$user || !$user->id) {
                return back()->with('error', 'Error de autenticación. Por favor, inicia sesión nuevamente.');
            }

            $cartItems = CartItem::where('user_id', $user->id)
                                ->with('product')
                                ->get();

            if ($cartItems->isEmpty()) {
                return back()->with('error', 'Tu carrito está vacío');
            }

            // Validar que cada item tenga producto válido
            foreach ($cartItems as $item) {
                if (!$item->product) {
                    return back()->with('error', 'Hay productos en tu carrito que ya no están disponibles');
                }
            }

            // Validar stock antes de crear la orden
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    return back()->with('error', 
                        "No hay stock suficiente para {$item->product->name}. " .
                        "Stock disponible: {$item->product->stock}"
                    );
                }
            }

            // Calcular totales de forma más segura
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if ($item->product && $item->quantity > 0 && $item->product->price > 0) {
                    $subtotal += $item->quantity * $item->product->price;
                }
            }
            
            $envio = $subtotal > 50000 ? 0 : 3000;
            $total = $subtotal + $envio;

            // Crear la orden (versión simplificada sin Mercado Pago)
            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            // Crear los items de la orden
            foreach ($cartItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // Reducir stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Limpiar el carrito
            CartItem::where('user_id', $user->id)->delete();

            Log::info('Checkout procesado exitosamente', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total' => $total,
            ]);

            // Redirigir a la página de órdenes con mensaje de éxito
            return redirect()->route('orders.index')
                ->with('success', '¡Pedido creado exitosamente! Tu orden #' . $order->id . ' está siendo procesada.');

        } catch (\Exception $e) {
            Log::error('Error procesando checkout', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 
                'Ocurrió un error al procesar tu pedido. Por favor, inténtalo nuevamente.'
            );
        }
    }

    /**
     * ✅ Página de éxito de pago
     *
     * Ruta: GET /payment/success
     */
    public function success(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');
            $externalReference = $request->get('external_reference');

            if (!$externalReference) {
                return redirect()->route('home')
                    ->with('error', 'No se pudo verificar el pago');
            }

            $order = \App\Models\Order::find($externalReference);
            
            if (!$order || $order->user_id !== Auth::id()) {
                return redirect()->route('home')
                    ->with('error', 'Orden no encontrada');
            }

            return view('checkout.success', compact('order', 'paymentId'));

        } catch (\Exception $e) {
            Log::error('Error en payment.success', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Error al procesar el resultado del pago');
        }
    }

    /**
     * ⏳ Página de pago pendiente
     *
     * Ruta: GET /payment/pending
     */
    public function pending(Request $request)
    {
        try {
            $preferenceId = $request->get('preference_id');
            $externalReference = $request->get('external_reference');

            if (!$externalReference) {
                return redirect()->route('home')
                    ->with('error', 'No se pudo verificar el pago');
            }

            $order = \App\Models\Order::find($externalReference);
            
            if (!$order || $order->user_id !== Auth::id()) {
                return redirect()->route('home')
                    ->with('error', 'Orden no encontrada');
            }

            return view('checkout.pending', compact('order', 'preferenceId'));

        } catch (\Exception $e) {
            Log::error('Error en payment.pending', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Error al procesar el resultado del pago');
        }
    }

    /**
     * ❌ Página de fallo de pago
     *
     * Ruta: GET /payment/failure
     */
    public function failure(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');
            $preferenceId = $request->get('preference_id');
            $externalReference = $request->get('external_reference');

            if (!$externalReference) {
                return redirect()->route('home')
                    ->with('error', 'No se pudo verificar el pago');
            }

            $order = \App\Models\Order::find($externalReference);
            
            if (!$order || $order->user_id !== Auth::id()) {
                return redirect()->route('home')
                    ->with('error', 'Orden no encontrada');
            }

            // Actualizar estado de la orden
            $order->update(['status' => 'cancelled']);

            return view('checkout.failure', compact('order', 'paymentId', 'preferenceId'));

        } catch (\Exception $e) {
            Log::error('Error en payment.failure', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Error al procesar el resultado del pago');
        }
    }
}