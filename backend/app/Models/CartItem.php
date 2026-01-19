<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 🛒 CartItem - Elemento del Carrito de Compras
 *
 * Representa un producto específico en el carrito de un usuario
 */
class CartItem extends Model
{
    use HasFactory;

    /**
     * Los campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    /**
     * Los campos que se deben convertir a tipos nativos
     */
    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 🧑 Relación: Obtener el usuario del carrito
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 📦 Relación: Obtener el producto del carrito
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 💰 Calcular el subtotal del item (precio × cantidad)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->product->price * $this->quantity;
    }

    /**
     * 🧮 Método estático: Obtener el carrito del usuario actual
     */
    public static function getCartItems(): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('product')->where('user_id', auth()->id())->get();
    }

    /**
     * 🧮 Método estático: Calcular el total del carrito del usuario actual
     */
    public static function getCartTotal(): float
    {
        return static::getCartItems()->sum(function ($item) {
            return $item->subtotal;
        });
    }

    /**
     * 🧮 Método estático: Obtener la cantidad total de items en el carrito
     */
    public static function getCartItemCount(): int
    {
        return static::getCartItems()->sum('quantity');
    }
}
