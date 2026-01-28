<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * OutOfStockSeeder - Crea producto sin stock para pruebas
 */
class OutOfStockSeeder extends Seeder
{
    public function run(): void
    {
        // Actualizar el primer producto para que tenga stock 0
        Product::where('id', 1)->update(['stock' => 0]);
        
        $this->command->info('✅ Producto ID 1 actualizado a stock 0 para pruebas TC-004');
    }
}