<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * 🌱 CategorySeeder - Datos de Categorías de Prueba
 *
 * Este seeder crea las categorías iniciales de PET CUTE CLOTHES.
 *
 * Un "seeder" es como "semilla" que plantamos en la base de datos
 * para que no empiece vacía. Es como plantar árboles pequeños
 * que luego crecen y se convierten en un bosque. 🌳
 *
 * Categorías que vamos a crear:
 * 1. 🌞 Casual - Ropa para el día a día
 * 2. ✨ Elegante - Ropa para eventos especiales
 * 3. 🎂 Cumpleaños - Ropa para fiestas
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este método se ejecuta cuando corremos: php artisan db:seed
     *
     * Crea las categorías iniciales del sistema.
     */
    public function run(): void
    {
        // 📋 Array de categorías a crear
        //
        // Cada elemento representa una categoría con su nombre
        $categorias = [
            [
                'name' => 'Casual',
            ],
            [
                'name' => 'Elegante',
            ],
            [
                'name' => 'Cumpleaños',
            ],
        ];

        // 🌱 CREAR cada categoría en la base de datos
        //
        // Recorremos el array y para cada categoría:
        // 1. Creamos un objeto Category
        // 2. Llenamos el campo 'name'
        // 3. Guardamos en la base de datos
        foreach ($categorias as $categoria) {
            Category::create($categoria);
        }

        // ✅ Mensaje de confirmación (se ve al ejecutar el seeder)
        $this->command->info('✅ Categorías creadas exitosamente!');
        $this->command->info('   - 🌞 Casual: Ropa para el día a día');
        $this->command->info('   - ✨ Elegante: Ropa para eventos especiales');
        $this->command->info('   - 🎂 Cumpleaños: Ropa para fiestas');
    }
}
