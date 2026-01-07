<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 📁 Modelo Category - Representa una CATEGORÍA
 *
 * Este modelo representa una categoría de productos:
 * - Casual: Ropa para el día a día 🌞
 * - Elegante: Ropa para eventos especiales ✨
 * - Cumpleaños: Ropa para fiestas 🎂
 *
 * Un modelo en Laravel es como una "muñeca" o "dibujo"
 * que representa algo de la vida real en tu código.
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Estos son los campos que podemos LLENAR o MODIFICAR
     *
     * El atributo $fillable es como una lista de "puertas abiertas".
     * Si un campo está en esta lista, podemos modificarlo.
     * Si NO está, Laravel lo protege (por seguridad).
     */
    protected $fillable = [
        'name',  // Nombre de la categoría (ej: "Casual")
    ];

     /**
     * 📦 RELACIÓN: Una categoría tiene MUCHOS productos
     *
     * Esta función dice: "Una categoría tiene muchos productos"
     *
     * Ejemplo práctico:
     * Categoría "Casual" tiene:
     *   - Suéter con corazones
     *   - Camiseta básica
     *   - Chaqueta ligera
     *   ...y muchos más productos
     *
     * NOTA: Creamos el modelo Product después, así que esta relación
     * se usará cuando existan ambos modelos.
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
     * 📌 UTILIDAD: Obtener nombre formateado
     *
     * Esta función es un "atajo" o "helper" para obtener
     * el nombre de la categoría con mayúscula inicial.
     *
     * Uso: $categoria->nombreFormateado()
     * Retorna: "Casual" (con mayúscula inicial)
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst($this->name);  // ucfirst = Upper Case First (primera letra mayúscula)
    }
}
