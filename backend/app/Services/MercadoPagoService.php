<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

/**
 * 💳 MercadoPagoService - Servicio de integración con Mercado Pago
 *
 * Este servicio maneja toda la comunicación con la API de Mercado Pago
 * para crear preferencias de pago y procesar webhooks.
 */
class MercadoPagoService
{
    private $preferenceClient;
    private $paymentClient;

    public function __construct()
    {
        // Configurar acceso a Mercado Pago
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        MercadoPagoConfig::setPlatformId(config('services.mercadopago.platform_id', 'mp'));
        
        $this->preferenceClient = new PreferenceClient();
        $this->paymentClient = new PaymentClient();
    }

    /**
     * 🛒 Crear preferencia de pago para una orden
     *
     * @param Order $order
     * @return array
     */
    public function createPreference(Order $order)
    {
        try {
            // Construir items para Mercado Pago
            $items = [];
            
            foreach ($order->items as $orderItem) {
                $items[] = [
                    "id" => $orderItem->product->id,
                    "title" => $orderItem->product->name,
                    "description" => $orderItem->product->description,
                    "quantity" => $orderItem->quantity,
                    "unit_price" => (float) $orderItem->price,
                    "currency_id" => "ARS",
                    "picture_url" => $orderItem->product->imagenUrl,
                ];
            }

            // Configurar URLs de retorno
            $backUrls = [
                "success" => route('payment.success'),
                "failure" => route('payment.failure'),
                "pending" => route('payment.pending'),
            ];

            // Crear preferencia
            $request = [
                "items" => $items,
                "external_reference" => (string) $order->id,
                "back_urls" => $backUrls,
                "auto_return" => "approved",
                "binary_mode" => false,
                "statement_descriptor" => config('app.name', 'PETCUTECLOTHE'),
                "expires" => false,
                "date_of_expiration" => null,
                "differential_pricing" => null,
                "payment_methods" => [
                    "excluded_payment_types" => [],
                    "excluded_payment_methods" => [],
                    "installments" => 12,
                    "default_payment_method_id" => null,
                    "default_installments" => null,
                ],
                "shipments" => [
                    "receiver_address" => [
                        "zip_code" => "",
                        "street_name" => "",
                        "street_number" => null,
                        "floor" => "",
                        "apartment" => "",
                    ],
                ],
                "notification_url" => route('webhook.mercadopago'),
            ];

            $preference = $this->preferenceClient->create($request);

            // Guardar información del pago en la base de datos
            Payment::create([
                'order_id' => $order->id,
                'mercado_pago_id' => $preference['id'],
                'status' => 'pending',
                'amount' => $order->total,
                'payment_method_id' => null,
                'payment_type_id' => null,
                'merchant_order_id' => null,
                'preference_id' => $preference['id'],
                'init_point' => $preference['init_point'],
                'sandbox_init_point' => $preference['sandbox_init_point'] ?? null,
            ]);

            Log::info('Preferencia de Mercado Pago creada', [
                'order_id' => $order->id,
                'preference_id' => $preference['id'],
                'amount' => $order->total,
            ]);

            return [
                'success' => true,
                'preference_id' => $preference['id'],
                'init_point' => $preference['init_point'],
                'sandbox_init_point' => $preference['sandbox_init_point'] ?? null,
            ];

        } catch (MPApiException $e) {
            Log::error('Error al crear preferencia de Mercado Pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'response' => $e->getApiResponse()->getContent(),
            ]);

            return [
                'success' => false,
                'error' => 'No se pudo procesar el pago. Intente nuevamente.',
            ];
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear preferencia', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Error en el sistema. Contacte al administrador.',
            ];
        }
    }

    /**
     * 🔍 Obtener información de un pago
     *
     * @param string $paymentId
     * @return array|null
     */
    public function getPaymentInfo(string $paymentId): ?array
    {
        try {
            $payment = $this->paymentClient->get($paymentId);
            return $payment;
        } catch (MPApiException $e) {
            Log::error('Error al obtener información del pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error inesperado al obtener pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 🪝 Procesar webhook de Mercado Pago
     *
     * @param array $data
     * @return array
     */
    public function processWebhook(array $data): array
    {
        try {
            $type = $data['type'] ?? '';
            $dataId = $data['data'] ?? ['id' => null];
            $paymentId = $dataId['id'] ?? null;

            if ($type === 'payment' && $paymentId) {
                return $this->processPaymentWebhook($paymentId);
            }

            if ($type === 'merchant_order') {
                Log::info('Webhook de merchant_order recibido', [
                    'type' => $type,
                    'data' => $dataId,
                ]);
                return ['success' => true, 'message' => 'merchant_order procesado'];
            }

            Log::warning('Tipo de webhook no procesado', [
                'type' => $type,
                'data' => $dataId,
            ]);

            return ['success' => false, 'message' => 'Tipo de webhook no soportado'];

        } catch (\Exception $e) {
            Log::error('Error procesando webhook', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return ['success' => false, 'error' => 'Error procesando webhook'];
        }
    }

    /**
     * 💳 Procesar webhook específico de pago
     *
     * @param string $paymentId
     * @return array
     */
    private function processPaymentWebhook(string $paymentId): array
    {
        $paymentInfo = $this->getPaymentInfo($paymentId);
        
        if (!$paymentInfo) {
            return ['success' => false, 'error' => 'Pago no encontrado'];
        }

        $externalReference = $paymentInfo['external_reference'] ?? null;
        $status = $paymentInfo['status'] ?? null;
        $statusDetail = $paymentInfo['status_detail'] ?? null;
        $paymentTypeId = $paymentInfo['payment_type_id'] ?? null;
        $merchantOrderId = $paymentInfo['merchant_order_id'] ?? null;

        if (!$externalReference) {
            Log::error('Pago sin referencia externa', [
                'payment_id' => $paymentId,
                'payment_info' => $paymentInfo,
            ]);
            return ['success' => false, 'error' => 'Referencia externa no encontrada'];
        }

        // Buscar la orden
        $order = Order::find($externalReference);
        if (!$order) {
            Log::error('Orden no encontrada para el pago', [
                'payment_id' => $paymentId,
                'external_reference' => $externalReference,
            ]);
            return ['success' => false, 'error' => 'Orden no encontrada'];
        }

        // Buscar o crear el registro de pago
        $payment = Payment::where('order_id', $order->id)
                          ->where('mercado_pago_id', $paymentId)
                          ->first();

        if (!$payment) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'mercado_pago_id' => $paymentId,
                'status' => $status,
                'amount' => $paymentInfo['transaction_amount'] ?? 0,
                'payment_method_id' => $paymentInfo['payment_method_id'],
                'payment_type_id' => $paymentTypeId,
                'merchant_order_id' => $merchantOrderId,
                'status_detail' => $statusDetail,
            ]);
        } else {
            $payment->update([
                'status' => $status,
                'status_detail' => $statusDetail,
                'merchant_order_id' => $merchantOrderId,
            ]);
        }

        // Actualizar el estado de la orden según el estado del pago
        $this->updateOrderStatus($order, $status, $statusDetail);

        Log::info('Pago procesado correctamente', [
            'payment_id' => $paymentId,
            'order_id' => $order->id,
            'status' => $status,
            'status_detail' => $statusDetail,
        ]);

        return ['success' => true, 'message' => 'Pago procesado correctamente'];
    }

    /**
     * 📋 Actualizar estado de la orden según estado del pago
     *
     * @param Order $order
     * @param string $paymentStatus
     * @param string $statusDetail
     * @return void
     */
    private function updateOrderStatus(Order $order, string $paymentStatus, string $statusDetail): void
    {
        $orderStatus = match ($paymentStatus) {
            'approved' => 'processing',
            'pending' => 'pending',
            'rejected' => 'cancelled',
            'cancelled' => 'cancelled',
            'refunded' => 'cancelled',
            'charged_back' => 'cancelled',
            default => $order->status,
        };

        // Si el pago fue aprobado pero está en proceso de validación
        if ($paymentStatus === 'approved' && in_array($statusDetail, 'pending_contingency', 'pending_review_manual')) {
            $orderStatus = 'pending';
        }

        $order->update(['status' => $orderStatus]);

        // Aquí podríamos enviar notificaciones por email
        // NotificationService::paymentProcessed($order, $paymentStatus);
    }

    /**
     * 🧹 Cancelar preferencia de pago
     *
     * @param string $preferenceId
     * @return bool
     */
    public function cancelPreference(string $preferenceId): bool
    {
        try {
            $this->preferenceClient->update($preferenceId, [
                'expires' => true,
                'date_of_expiration' => now()->subMinutes(1)->format('c'),
            ]);

            Log::info('Preferencia cancelada', ['preference_id' => $preferenceId]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error cancelando preferencia', [
                'preference_id' => $preferenceId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}