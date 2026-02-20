<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_principal_carga_correctamente(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PET CUTE CLOTHES');
    }

    public function test_pagina_de_productos_carga(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_registro_de_usuario(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan',
            'lastname' => 'Perez',
            'email' => 'juan@test.com',
            'dni' => '12345678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@test.com',
            'name' => 'Juan',
            'lastname' => 'Perez',
        ]);

        $response->assertRedirect();
    }

    public function test_login_de_usuario(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_carrito_agregar_producto(): void
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'price' => 1000,
            'category_id' => $category->id,
            'stock' => 10,
            'image_url' => 'test.jpg',
            'size' => 1,
        ]);

        $response = $this->post("/cart/add/{$product->id}", [
            'quantity' => 2,
            'size' => 1,
        ]);

        $response->assertSessionHas('success');
    }

    public function test_carrito_ver_contenido(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
    }

    public function test_protegido_para_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_con_auth(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@test.com',
        ]);

        $this->actingAs($admin);
        
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_crear_categoria_como_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Nueva Categoria Test',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Nueva Categoria Test',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_editar_categoria_como_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $category = Category::create([
            'name' => 'Categoria Original',
            'slug' => 'categoria-original',
        ]);

        $response = $this->actingAs($admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Categoria Editada',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Categoria Editada',
        ]);
    }

    public function test_eliminar_categoria_sin_productos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $category = Category::create([
            'name' => 'Categoria para eliminar',
            'slug' => 'categoria-para-eliminar',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_no_se_puede_eliminar_categoria_con_productos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $category = Category::create([
            'name' => 'Categoria con productos',
            'slug' => 'categoria-con-productos',
        ]);

        Product::create([
            'name' => 'Producto en categoria',
            'slug' => 'producto-en-categoria',
            'description' => 'Test',
            'price' => 1000,
            'category_id' => $category->id,
            'stock' => 10,
            'image_url' => 'test.jpg',
            'size' => 1,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
