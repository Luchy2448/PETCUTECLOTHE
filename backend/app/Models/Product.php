<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 📦 Modelo Product - Representa un PRODUCTO
 *
 * Este modelo representa un producto de ropa para mascotas:
 * - "Suéter con corazones"
 * - "Vestido de gala"
 * - "Corbata elegante"
 *
 * Un modelo en Laravel es como una "muñeca" o "plantilla"
 * que representa algo de la vida real en tu código.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Estos son los campos que podemos LLENAR o MODIFICAR
     *
     * El atributo $fillable es como una lista de "puertas abiertas".
     * Si un campo está en esta lista, podemos modificarlo.
     * Si NO está, Laravel lo protege (por seguridad).
     *
     * Campos permitidos:
     * - name: Nombre del producto (ej: "Suéter con corazones")
     * - description: Descripción detallada
     * - price: Precio (ej: 15000)
     * - stock: Cantidad disponible (ej: 10)
     * - size: Talla (1, 2, 3, 4, 5)
     * - category_id: ID de la categoría (ej: 1 = "Casual")
     * - image_url: URL de la foto
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'size',
        'category_id',
        'image_url'
    ];

    /**
     * 📁 RELACIÓN: Un producto PERTENECE A una categoría
     *
     * Esta función dice: "Este producto tiene una categoría mamá"
     *
     * Ejemplo práctico:
     * Producto "Suéter con corazones" pertenece a:
     * - Categoría "Casual"
     *
     * Tipo de relación: BelongsTo = "Pertenece a"
     * Es decir, un producto tiene SOLO UNA categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 💰 UTILIDAD: Obtener precio formateado
     *
     * Esta función es un "atajo" o "helper" para obtener
     * el precio del producto formateado con signo de pesos.
     *
     * Uso: $producto->precioFormateado
     * Retorna: "$15,000.00"
     *
     * ¿Cómo funciona?
     * - number_format: Formatea el número
     * - $this->price: El precio del producto (ej: 15000)
     * - 0: Cantidad de decimales
     * - ',': Separador de miles (coma)
     * - '.': Separador de decimales (punto)
     */
    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format($this->price, 0, ',', '.');
    }

    /**
     * 📏 UTILIDAD: Obtener talla como texto
     *
     * Esta función convierte la talla numérica (1, 2, 3, 4, 5)
     * a texto (XS, S, M, L, XL) para que sea más fácil de entender.
     *
     * Uso: $producto->tallaTexto
     * Retorna: "M" (si la talla es 3)
     *
     * Tallas:
     * 1 = XS (Extra Small)
     * 2 = S (Small)
     * 3 = M (Medium)
     * 4 = L (Large)
     * 5 = XL (Extra Large)
     */
    public function getTallaTextoAttribute(): string
    {
        $tallas = [
            1 => 'XS',
            2 => 'S',
            3 => 'M',
            4 => 'L',
            5 => 'XL'
        ];

        return $tallas[$this->size] ?? $this->size;
    }

    /**
     * ✅ UTILIDAD: Verificar si hay stock
     *
     * Esta función verifica si el producto está disponible.
     *
     * Uso: $producto->enStock
     * Retorna: true (si hay más de 0) o false (si no hay)
     */
    public function getEnStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * 🎨 UTILIDAD: Obtener URL de imagen por defecto
     *
     * Esta función devuelve la URL de la imagen,
     * pero si no tiene, devuelve una imagen por defecto.
     *
     * Uso: $producto->imagenUrl
     */
    public function getImagenUrlAttribute(): string
    {
        return $this->image_url ?? 'https://via.placeholder.com/300x300?text=Sin+Imagen';
    }

    /**
     * 🔍 SCOPE: Búsqueda de productos
     *
     * Permite buscar productos por nombre, descripción o marca
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('brand', 'LIKE', "%{$search}%");
        });
    }

    /**
     * 📊 SCOPE: Ordenamiento
     *
     * Permite ordenar productos por diferentes criterios
     */
    public function scopeSort($query, $sortBy)
    {
        return match ($sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'relevance' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * 🏷️ SCOPE: Filtrar por categoría
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (!$categoryId) return $query;
        return $query->where('category_id', $categoryId);
    }

    /**
     * 💰 SCOPE: Filtrar por rango de precios
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * 📏 SCOPE: Filtrar por talla
     */
    public function scopeBySize($query, $size)
    {
        if (!$size) return $query;
        return $query->where('size', $size);
    }

    /**
     * ✅ SCOPE: Solo productos con stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
