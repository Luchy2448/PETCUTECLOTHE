<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 📦 ProductSeeder - Datos de Productos de Prueba
 *
 * Este seeder crea los productos iniciales de PET CUTE CLOTHES.
 *
 * Un "seeder" es como "semilla" que plantamos en la base de datos
 * para que no empiece vacía. Es como plantar árboles pequeños
 * que luego crecen y se convierten en un bosque. 🌳
 *
 * Productos que vamos a crear:
 * - 3 productos casuales (para el día a día)
 * - 3 productos elegantes (para eventos especiales)
 * - 2 productos de cumpleaños (para fiestas)
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este método se ejecuta cuando corremos: php artisan db:seed
     *
     * Crea los productos iniciales del sistema.
     */
    public function run(): void
    {
        // 📋 Array de productos a crear
        //
        // Cada elemento representa un producto con toda su información:
        // - name: Nombre del producto
        // - description: Descripción detallada
        // - price: Precio en pesos argentinos
        // - stock: Cantidad disponible
        // - size: Talla (1, 2, 3, 4, 5)
        // - category_id: ID de la categoría a la que pertenece
        // - image_url: URL de la foto (usamos placeholder)
        $productos = [
            // 🌞 CATEGORÍA: CASUAL (ID = 1)
            [
                'name' => 'Suéter con corazones',
                'description' => 'Lindo suéter para gatito con adorable diseño de corazones. Perfecto para días fríos. Material suave y cómodo para tu peludo amigo.',
                'price' => 15000,
                'stock' => 10,
                'size' => 3,
                'category_id' => 1,  // Casual
                'image_url' => 'https://via.placeholder.com/300x300/FFB6C1/ffffff?text=Suéter+Corazones'
            ],
            [
                'name' => 'Camiseta básica',
                'description' => 'Camiseta sencilla y cómoda para tu mascota. Ideal para días calurosos o para usar como base. Varios colores disponibles.',
                'price' => 8000,
                'stock' => 15,
                'size' => 2,
                'category_id' => 1,  // Casual
                'image_url' => 'https://via.placeholder.com/300x300/ADD8E6/ffffff?text=Camiseta+Básica'
            ],
            [
                'name' => 'Chaqueta ligera',
                'description' => 'Chaqueta ligera para paseos al aire libre. Protege del viento sin incomodar a tu mascota. Diseño moderno y estiloso.',
                'price' => 20000,
                'stock' => 5,
                'size' => 4,
                'category_id' => 1,  // Casual
                'image_url' => 'https://via.placeholder.com/300x300/98FF98/ffffff?text=Chaqueta+Ligera'
            ],
            
            // ✨ CATEGORÍA: ELEGANTE (ID = 2)
            [
                'name' => 'Vestido de gala',
                'description' => 'Elegante vestido para eventos especiales. Con brillantes y detalles que harán que tu mascota sea la más linda de la fiesta. Tela premium.',
                'price' => 25000,
                'stock' => 3,
                'size' => 3,
                'category_id' => 2,  // Elegante
                'image_url' => 'https://via.placeholder.com/300x300/FFFACD/ffffff?text=Vestido+de+Gala'
            ],
            [
                'name' => 'Corbata elegante',
                'description' => 'Corbata de diseño elegante para perros y gatos pequeños. Perfecta para bodas, cumpleaños y eventos formales. Ajuste fácil.',
                'price' => 5000,
                'stock' => 8,
                'size' => 1,
                'category_id' => 2,  // Elegante
                'image_url' => 'https://via.placeholder.com/300x300/FFD700/ffffff?text=Corbata+Elegante'
            ],
            [
                'name' => 'Sombrero de fiesta',
                'description' => 'Sombrero estilizado para tu mascota. Complemento perfecto para fotos especiales y eventos. No molesta a tu peludo amigo.',
                'price' => 7000,
                'stock' => 6,
                'size' => 2,
                'category_id' => 2,  // Elegante
                'image_url' => 'https://via.placeholder.com/300x300/4F46E5/ffffff?text=Sombrero+de+Fiesta'
            ],
            
            // 🎂 CATEGORÍA: CUMPLEAÑOS (ID = 3)
            [
                'name' => 'Disfraz de superhéroe',
                'description' => '¡Convierte a tu mascota en un superhéroe! Disfraz completo con capa y máscara. Ideal para cumpleaños temáticos.',
                'price' => 18000,
                'stock' => 4,
                'size' => 3,
                'category_id' => 3,  // Cumpleaños
                'image_url' => 'https://via.placeholder.com/300x300/EF4444/ffffff?text=Superhéroe'
            ],
            [
                'name' => 'Tutu rosa',
                'description' => 'Encantador tutu rosa para tu mascota. Perfecto para fotos de cumpleaño y sesiones fotográficas. Material cómodo y suave.',
                'price' => 12000,
                'stock' => 7,
                'size' => 2,
                'category_id' => 3,  // Cumpleaños
                'image_url' => 'https://via.placeholder.com/300x300/FF69B4/ffffff?text=Tutu+Rosa'
            ],
        ];

        // 📦 CREAR cada producto en la base de datos
        //
        // Recorremos el array y para cada producto:
        // 1. Creamos un objeto Product
        // 2. Llenamos todos los campos con los datos
        // 3. Guardamos en la base de datos
        foreach ($productos as $producto) {
            Product::create($producto);
        }

        // ✅ Mensaje de confirmación (se ve al ejecutar el seeder)
        $this->command->info('✅ Productos creados exitosamente!');
        $this->command->info('📦 Total de productos creados: ' . count($productos));
        $this->command->info('🌞 Productos casuales: 3');
        $this->command->info('✨ Productos elegantes: 3');
        $this->command->info('🎂 Productos de cumpleaños: 2');
        $this->command->info('');
        $this->command->info('💰 Valor total del inventario: $' . number_format(15000*10 + 8000*15 + 20000*5 + 25000*3 + 5000*8 + 7000*6 + 18000*4 + 12000*7, 0, ',', '.'));

        // 👤 CREAR usuario admin para pruebas
        //
        // Creamos un usuario administrador para que puedas loguearte
        // y probar todas las funcionalidades del sistema.
        User::create([
            'name' => 'Admin',
            'email' => 'admin@petcute.com',
            'password' => Hash::make('password123')
        ]);

        $this->command->info('');
        $this->command->info('👤 Usuario admin creado:');
        $this->command->info('   Email: admin@petcute.com');
        $this->command->info('   Contraseña: password123');
        $this->command->info('');
        $this->command->info('🎉 ¡Ahora puedes loguearte con este usuario!');
    }
}
