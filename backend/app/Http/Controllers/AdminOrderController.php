<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * 🛡️ AdminOrderController - Controlador administrativo de órdenes
 *
 * Permite a los administradores gestionar todas las órdenes del sistema
 */
class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * 📋 Listado de órdenes con filtros
     *
     * Ruta: GET /admin/orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'payments'])
                     ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->status) {
            $query->byStatus($request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20);

        // Estadísticas
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'month_revenue' => Order::where('status', 'delivered')
                                   ->where('created_at', '>=', now()->subMonth())
                                   ->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * 👁️ Mostrar detalles de una orden
     *
     * Ruta: GET /admin/orders/{order}
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payments']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * 🔄 Actualizar estado de la orden
     *
     * Ruta: PATCH /admin/orders/{order}/status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Validar transiciones de estado
        if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
            return back()
                ->with('error', "No se puede cambiar de '{$oldStatus}' a '{$newStatus}'");
        }

        try {
            DB::beginTransaction();

            // Actualizar estado
            $updateData = ['status' => $newStatus];

            if ($request->tracking_number) {
                $updateData['tracking_number'] = $request->tracking_number;
            }

            if ($request->notes) {
                $updateData['notes'] = $request->notes;
            }

            // Agregar timestamps según el estado
            match ($newStatus) {
                'shipped' => $updateData['shipped_at'] = now(),
                'delivered' => $updateData['delivered_at'] = now(),
                'cancelled' => $updateData['cancelled_at'] = now(),
                default => null,
            };

            $order->update($updateData);

            // Si se cancela, devolver stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            Log::info('Estado de orden actualizado', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'admin_id' => Auth::id(),
            ]);

            // Aquí podríamos enviar notificación por email
            // NotificationService::orderStatusChanged($order);

            return back()->with('success', 
                "Estado actualizado a '{$order->statusText}' correctamente");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error actualizando estado de orden', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 
                'Error al actualizar el estado. Intente nuevamente.');
        }
    }

    /**
     * 🚚 Marcar orden como enviada (con número de seguimiento)
     *
     * Ruta: POST /admin/orders/{order}/ship
     */
    public function ship(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        if (!in_array($order->status, ['processing'])) {
            return back()->with('error', 
                'Solo se pueden enviar órdenes en estado "En proceso"');
        }

        try {
            $order->markAsShipped($request->tracking_number);

            Log::info('Orden enviada', [
                'order_id' => $order->id,
                'tracking_number' => $request->tracking_number,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 
                'Orden marcada como enviada correctamente');

        } catch (\Exception $e) {
            Log::error('Error marcando orden como enviada', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 
                'Error al marcar la orden como enviada');
        }
    }

    /**
     * ✅ Marcar orden como entregada
     *
     * Ruta: POST /admin/orders/{order}/deliver
     */
    public function deliver(Order $order)
    {
        if (!in_array($order->status, ['shipped'])) {
            return back()->with('error', 
                'Solo se pueden entregar órdenes en estado "Enviado"');
        }

        try {
            $order->markAsDelivered();

            Log::info('Orden entregada', [
                'order_id' => $order->id,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 
                'Orden marcada como entregada correctamente');

        } catch (\Exception $e) {
            Log::error('Error marcando orden como entregada', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 
                'Error al marcar la orden como entregada');
        }
    }

    /**
     * ❌ Cancelar orden
     *
     * Ruta: POST /admin/orders/{order}/cancel
     */
    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if (!$order->canBeCancelled()) {
            return back()->with('error', 
                'Esta orden no puede ser cancelada');
        }

        try {
            DB::beginTransaction();

            // Devolver stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Cancelar orden
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'notes' => ($order->notes ?? '') . "\n\nCancelado por admin: " . $request->cancellation_reason,
            ]);

            DB::commit();

            Log::info('Orden cancelada por admin', [
                'order_id' => $order->id,
                'reason' => $request->cancellation_reason,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 
                'Orden cancelada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error cancelando orden', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 
                'Error al cancelar la orden');
        }
    }

    /**
     * 📊 Dashboard de estadísticas
     *
     * Ruta: GET /admin/orders/stats
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year

        $startDate = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        // Estadísticas básicas
        $stats = [
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'completed_orders' => Order::where('created_at', '>=', $startDate)
                                      ->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('created_at', '>=', $startDate)
                                      ->where('status', 'cancelled')->count(),
            'revenue' => Order::where('created_at', '>=', $startDate)
                            ->where('status', 'delivered')
                            ->sum('total'),
            'average_order_value' => Order::where('created_at', '>=', $startDate)
                                         ->where('status', 'delivered')
                                         ->avg('total'),
        ];

        // Órdenes por estado
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
                              ->where('created_at', '>=', $startDate)
                              ->groupBy('status')
                              ->pluck('count', 'status')
                              ->toArray();

        // Revenue diario
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
                           ->where('created_at', '>=', $startDate)
                           ->where('status', 'delivered')
                           ->groupBy('date')
                           ->orderBy('date')
                           ->get();

        // Productos más vendidos
        $topProducts = DB::table('order_items')
                        ->select('product_id', 
                                DB::raw('COUNT(*) as orders_count'),
                                DB::raw('SUM(quantity) as total_quantity'),
                                DB::raw('SUM(quantity * price) as total_revenue'))
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.created_at', '>=', $startDate)
                        ->where('orders.status', 'delivered')
                        ->groupBy('product_id')
                        ->orderBy('total_revenue', 'desc')
                        ->limit(10)
                        ->get();

        // Clientes con más compras
        $topCustomers = Order::select('user_id',
                            DB::raw('COUNT(*) as orders_count'),
                            DB::raw('SUM(total) as total_spent'))
                           ->where('created_at', '>=', $startDate)
                           ->where('status', 'delivered')
                           ->groupBy('user_id')
                           ->orderBy('total_spent', 'desc')
                           ->limit(10)
                           ->with('user')
                           ->get();

        return view('admin.orders.stats', compact(
            'stats', 
            'ordersByStatus', 
            'dailyRevenue', 
            'topProducts', 
            'topCustomers',
            'period'
        ));
    }

    /**
     * 🔍 Validar transición de estados
     */
    private function isValidStatusTransition(string $from, string $to): bool
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [], // No se puede cambiar desde entregado
            'cancelled' => [], // No se puede cambiar desde cancelado
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }
}