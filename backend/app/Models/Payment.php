<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment - Pago de Pedido
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'mercado_pago_preference_id',
        'mercado_pago_payment_id',
        'status',
        'payment_method',
        'payment_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener pedido asociado
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Verificar si el pago está aprobado
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si el pago fue rechazado
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Obtener estado formateado para display
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge bg-warning text-dark',
            'approved' => 'badge bg-success text-white',
            'rejected' => 'badge bg-danger text-white',
            default => 'badge bg-secondary text-white',
        };
    }

    /**
     * Obtener texto del estado
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            default => 'Desconocido',
        };
    }
}