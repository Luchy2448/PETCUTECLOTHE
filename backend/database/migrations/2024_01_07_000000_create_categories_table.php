<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 📁 Migración para crear la tabla de CATEGORÍAS
 *
 * Esta migración crea la tabla donde guardamos las categorías
 * de los productos: "Casual", "Elegante", "Cumpleaños", etc.
 *
 * Ejemplo de uso de categoría:
 * - Casual: Ropa para el día a día 🌞
 * - Elegante: Ropa para eventos especiales ✨
 * - Cumpleaños: Ropa para fiestas 🎂
 */
return new class extends Migration
{
    /**
     * Esta función 🏗️ CREA la tabla de categorías
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                        // ID único (1, 2, 3, 4...)
            $table->string('name');               // Nombre de la categoría (ej: "Casual")
            $table->timestamps();                  // Fecha de creación y modificación
        });
    }

    /**
     * Esta función 🗑️ BORRA la tabla de categorías
     * Se usa si queremos revertir la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
