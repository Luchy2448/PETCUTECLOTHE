<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * PaymentController - Integración con Mercado Pago
 *
 * Maneja la integración con Mercado Pago para:
 * - Crear preferencias de pago
 * - Recibir notificaciones via webhooks
 * - Actualizar estados de pagos
 */
class PaymentController extends Controller
{
    /**
     * 💳 MÉTODO: create - Crear preferencia de pago
     *
     * Crea una preferencia de pago para un pedido existente
     *
     * Ruta: POST /api/payment/create
     * Requiere: Token de autenticación
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
            ]);

            $order = Order::findOrFail($validated['order_id']);

            // Aquí integraríamos con Mercado Pago SDK
            // Por ahora, simulamos la creación
            $preferenceData = [
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->product->id,
                        'title' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->price_at_purchase,
                        'currency_id' => 'ARS',
                    ];
                })->toArray(),
                'back_urls' => [
                    'success' => route('orders.show', $order->id),
                    'failure' => route('orders.show', $order->id),
                    'pending' => route('orders.show', $order->id),
                ],
                'auto_return' => 'approved',
                'payment_methods' => [
                    'credit_card', 'debit_card', 'ticket', 'atm'
                ],
            ];

            // Simular ID de preferencia (en producción real usaríamos el SDK)
            $preferenceId = 'PREF_' . uniqid() . '_' . time();

            // Guardar preferencia en la base de datos
            $order->update([
                'mercado_pago_preference_id' => $preferenceId,
            ]);

            return response()->json([
                'message' => 'Preferencia de pago creada',
                'preference_id' => $preferenceId,
                'redirect_url' => "https://www.mercadopago.com.ar/checkout/v1/redirect?preference_id=$preferenceId"
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear preferencia de pago',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔗 MÉTODO: webhook - Recibir notificaciones de Mercado Pago
     *
     * Procesa las notificaciones de estado de pagos
     *
     * Ruta: POST /api/payment/webhook (pública)
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            // Validar tipo de notificación
            if (!$request->has('type') || !in_array($request->input('type'), ['payment', 'merchant_order'])) {
                return response()->json(['error' => 'Tipo de notificación inválido'], 400);
            }

            // Aquí procesaríamos la notificación según el tipo
            $notification = $request->all();

            // Simular procesamiento básico
            if ($request->input('type') === 'payment') {
                $paymentId = $notification['data']['id'];
                $status = $notification['data']['status']; // approved, rejected

                // Actualizar estado del pedido
                $order = Order::find($notification['data']['external_reference']);
                if ($order) {
                    $order->update(['status' => $status === 'approved' ? 'paid' : 'cancelled']);
                }

                // Crear registro de pago
                Payment::create([
                    'order_id' => $order->id,
                    'mercado_pago_payment_id' => $paymentId,
                    'status' => $status,
                    'payment_method' => $notification['data']['payment_type'] ?? 'mercadopago',
                    'payment_type' => 'credit_card',
                ]);

                return response()->json([
                    'message' => 'Pago procesado',
                    'status' => $status
                ]);
            }

            return response()->json([
                'message' => 'Notificación recibida',
                'notification' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en webhook',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}