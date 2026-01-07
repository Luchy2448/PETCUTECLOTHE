<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Este método se ejecuta cuando corremos: php artisan db:seed
     *
     * Llama a los seeders de categorías y productos para
     * poblar la base de datos con datos de prueba.
     */
    public function run(): void
    {
        // 🌱 Ejecutar el seeder de CATEGORÍAS
        //
        // Crea las categorías: Casual, Elegante, Cumpleaños
        $this->call([
            CategorySeeder::class,
        ]);

        // 📦 Ejecutar el seeder de PRODUCTOS
        //
        // Crea 8 productos de ejemplo (3 casuales, 3 elegantes, 2 de cumpleaños)
        $this->call([
            ProductSeeder::class,
        ]);

        // ✅ Mensaje de confirmación
        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('🌞 Categorías: 3');
        $this->command->info('📦 Productos: 8');
        $this->command->info('');
        $this->command->info('🎉 ¡La tienda ya tiene datos para comenzar!');
    }
}
