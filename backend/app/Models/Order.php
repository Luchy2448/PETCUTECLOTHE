<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * Order - Pedido del Cliente
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
        'phone',
        'phone_number',
        'notes',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'payment_method',
        'shipping_name',
        'shipping_lastname',
        'shipping_dni',
        'mercado_pago_preference_id',
        'mercado_pago_payment_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * 📊 Estados posibles de la orden
     */
    public const STATUSES = [
        'pending' => 'Pendiente',
        'processing' => 'En proceso',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    /**
     * 🎨 Colores para badges Bootstrap
     */
    public const STATUS_BADGES = [
        'pending' => 'bg-warning text-dark',
        'processing' => 'bg-info text-white',
        'shipped' => 'bg-primary text-white',
        'delivered' => 'bg-success text-white',
        'cancelled' => 'bg-danger text-white',
    ];

    /**
     * Obtener items del pedido
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Obtener pagos del pedido
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Obtener usuario del pedido
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Verificar si el pedido puede ser cancelado
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * 🚚 Marcar como enviado
     */
    public function markAsShipped(string $trackingNumber = null): void
    {
        $this->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipped_at' => now(),
        ]);
    }

    /**
     * ✅ Marcar como entregado
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * ❌ Marcar como cancelado
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * 🔄 Cambiar estado
     */
    public function updateStatus(string $newStatus): bool
    {
        if (!array_key_exists($newStatus, self::STATUSES)) {
            return false;
        }

        $updateData = ['status' => $newStatus];

        // Agregar timestamps según el estado
        match ($newStatus) {
            'shipped' => $updateData['shipped_at'] = now(),
            'delivered' => $updateData['delivered_at'] = now(),
            'cancelled' => $updateData['cancelled_at'] = now(),
            default => null,
        };

        return $this->update($updateData);
    }

    /**
     * 💳 Verificar si el pedido está pagado
     */
    public function isPaid(): bool
    {
        return $this->payments()
                   ->where('status', 'approved')
                   ->exists();
    }

    /**
     * 🎨 Obtener badge Bootstrap para el estado
     */
    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'bg-secondary text-white';
    }

    /**
     * 📝 Obtener texto del estado
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Desconocido';
    }

    /**
     * 📊 Scope para filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        if (!$status) return $query;
        return $query->where('status', $status);
    }

    /**
     * 📅 Scope para filtrar por fecha
     */
    public function scopeByDateRange($query, $fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        return $query;
    }

    /**
     * 📈 Scope para órdenes del último mes
     */
    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }

    /**
     * 💰 Calcular total formateado
     */
    public function getTotalFormateadoAttribute(): string
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }

    /**
     * 📦 Obtener cantidad total de items
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}