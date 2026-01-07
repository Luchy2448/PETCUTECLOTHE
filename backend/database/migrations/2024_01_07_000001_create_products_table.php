<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 📦 Migración para crear la tabla de PRODUCTOS
 *
 * Esta migración crea la tabla donde guardamos toda la información
 * de los productos (la ropa para mascotas)
 *
 * Campos que tiene cada producto:
 * - id: Identificador único
 * - name: Nombre del producto (ej: "Suéter con corazones")
 * - description: Descripción detallada
 * - price: Precio en pesos argentinos (ej: 15000.00)
 * - stock: Cantidad disponible (ej: 10)
 * - size: Talla del producto (1, 2, 3, 4, 5)
 * - category_id: ID de la categoría a la que pertenece (relación)
 * - image_url: URL de la foto del producto
 * - timestamps: Fechas de creación y modificación
 */
return new class extends Migration
{
    /**
     * Esta función 🏗️ CREA la tabla de productos
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();                            // ID único (1, 2, 3, 4...)
            $table->string('name');                     // Nombre del producto (texto)
            $table->text('description');                // Descripción (texto largo)
            $table->decimal('price', 10, 2);          // Precio (número con decimales)
                                                      // 10 dígitos en total, 2 decimales
                                                      // Ejemplo: 15000.00 (15 mil pesos)
            
            $table->integer('stock');                   // Cantidad disponible (número entero)
                                                      // Ejemplo: 10, 5, 20
            
            $table->integer('size');                    // Talla del producto (1, 2, 3, 4, 5)
                                                      // 1 = XS, 2 = S, 3 = M, 4 = L, 5 = XL
            
            $table->string('image_url');                // URL de la foto (texto)
                                                      // Ejemplo: "https://ejemplo.com/sueter.jpg"
            
            // RELACIÓN con la tabla de categorías
            $table->foreignId('category_id')             // Crear columna category_id
                  ->constrained('categories')            // Se conecta con tabla categories
                  ->onDelete('cascade');                 // Si borras categoría, se borran sus productos
                                                      // cascade = en cascada, afecta a los relacionados
            
            $table->timestamps();                       // Fechas de creación y modificación
                                                      // created_at y updated_at (se crean automáticamente)
        });
    }

    /**
     * Esta función 🗑️ BORRA la tabla de productos
     * Se usa si queremos revertir la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
