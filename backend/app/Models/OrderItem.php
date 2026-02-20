<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * OrderItem - Item de Pedido
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'price_at_purchase',
        'size',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_at_purchase' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Obtener pedido al que pertenece
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Obtener producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}