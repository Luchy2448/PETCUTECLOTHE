<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            
            // Envío gratis sobre $50.000
            $envio = $subtotal > 50000 ? 0 : 3000;
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
        // Validar los datos del checkout
        $validated = $request->validate([
            'email' => 'required|email',
            'shipping_method' => 'required|in:delivery,pickup',
            'payment_method' => 'required|in:cash,transfer,whatsapp',
            // Datos para entrega a domicilio
            'delivery_name' => 'nullable|string|max:255',
            'delivery_lastname' => 'nullable|string|max:255',
            'delivery_dni' => 'nullable|string|max:20',
            'delivery_phone' => 'nullable|string|max:20',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_city' => 'nullable|string|max:255',
            'delivery_zipcode' => 'nullable|string|max:20',
            // Datos para retiro en sucursal
            'pickup_name' => 'nullable|string|max:255',
            'pickup_lastname' => 'nullable|string|max:255',
            'pickup_dni' => 'nullable|string|max:20',
            'pickup_phone' => 'nullable|string|max:20',
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

            // Calcular totales
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if ($item->product && $item->quantity > 0 && $item->product->price > 0) {
                    $subtotal += $item->quantity * $item->product->price;
                }
            }
            
            // Determinar costo de envío según método
            $envio = 0;
            $shippingMethod = $validated['shipping_method'];
            $paymentMethod = $validated['payment_method'];
            
            if ($shippingMethod === 'delivery') {
                $envio = $subtotal > 50000 ? 0 : 3000;
            }
            
            $subtotalConEnvio = $subtotal + $envio;
            
            // Aplicar descuento 5% si es pago en efectivo
            $discount = 0;
            $total = $subtotalConEnvio;
            
            if ($paymentMethod === 'cash') {
                $discount = $subtotalConEnvio * 0.05;
                $total = $subtotalConEnvio - $discount;
            }

            // Preparar datos según método de envío
            $shippingData = [];
            if ($shippingMethod === 'delivery') {
                $shippingData = [
                    'shipping_name' => $validated['delivery_name'] ?? $user->name,
                    'shipping_lastname' => $validated['delivery_lastname'] ?? '',
                    'shipping_dni' => $validated['delivery_dni'] ?? '',
                    'shipping_phone' => $validated['delivery_phone'] ?? '',
                    'shipping_address' => $validated['delivery_address'] ?? '',
                    'shipping_city' => $validated['delivery_city'] ?? '',
                    'shipping_zipcode' => $validated['delivery_zipcode'] ?? '',
                ];
            } else {
                // Retiro en sucursal
                $shippingData = [
                    'shipping_name' => $validated['pickup_name'] ?? $user->name,
                    'shipping_lastname' => $validated['pickup_lastname'] ?? '',
                    'shipping_dni' => $validated['pickup_dni'] ?? '',
                    'shipping_phone' => $validated['pickup_phone'] ?? '',
                    'shipping_address' => 'Retiro en sucursal',
                    'shipping_city' => '',
                    'shipping_zipcode' => '',
                ];
            }

            // Crear la orden
            $paymentMethodLabel = match($paymentMethod) {
                'cash' => 'Efectivo (5% OFF)',
                'transfer' => 'Transferencia bancaria',
                'whatsapp' => 'Acordar por WhatsApp',
                default => 'No especificado',
            };
            
            $notes = ($validated['notes'] ?? '');
            $notes .= "\n\n=== INFORMACIÓN DEL PEDIDO ===";
            $notes .= "\nMétodo de envío: " . ($shippingMethod === 'delivery' ? 'Envío a domicilio' : 'Retiro en sucursal');
            $notes .= "\nMétodo de pago: " . $paymentMethodLabel;
            if ($discount > 0) {
                $notes .= "\nDescuento (5% OFF): -$" . number_format($discount, 0, ',', '.');
            }
            $notes .= "\nEmail: " . $validated['email'];
            
            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $shippingData['shipping_address'],
                'phone' => $shippingData['shipping_phone'],
                'phone_number' => $shippingData['shipping_phone'],
                'notes' => $notes,
                'payment_method' => $paymentMethod,
                'shipping_name' => $shippingData['shipping_name'],
                'shipping_lastname' => $shippingData['shipping_lastname'],
                'shipping_dni' => $shippingData['shipping_dni'] ?? '',
            ]);

            // Crear los items de la orden
            foreach ($cartItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'price_at_purchase' => $item->product->price,
                    'size' => $item->product->size ?? 'M',
                ]);

                // Reducir stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Limpiar el carrito
            CartItem::where('user_id', $user->id)->delete();

            // Enviar email de confirmación
            $order->load(['user', 'items.product']);
            
            Log::info('Intentando enviar email de confirmación', [
                'order_id' => $order->id,
                'email' => $validated['email'],
            ]);
            
            Mail::to($validated['email'])->send(new OrderConfirmation($order));
            
            Log::info('Email de confirmación enviado exitosamente', [
                'order_id' => $order->id,
                'email' => $validated['email'],
            ]);

            Log::info('Checkout procesado exitosamente', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total' => $total,
            ]);

            // Redirigir a la página de éxito
            return redirect()->route('checkout.success', ['order' => $order->id])
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
     * ✅ Página de éxito después del checkout
     *
     * Ruta: GET /checkout/success/{order}
     */
    public function checkoutSuccess(\App\Models\Order $order)
    {
        try {
            if (!$order || $order->user_id !== Auth::id()) {
                return redirect()->route('home')
                    ->with('error', 'Orden no encontrada');
            }

            $order->load(['user', 'items.product']);

            return view('checkout.success', compact('order'));

        } catch (\Exception $e) {
            Log::error('Error en checkout.success', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Error al cargar los detalles del pedido');
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