<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\ViewErrorBag;

class ExploratoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        view()->share('errors', new ViewErrorBag());
    }

    public function test_exploratory_user_flow(): void
    {
        // 1. Visit homepage
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PET CUTE CLOTHES');

        // 2. Visit products page
        $response = $this->get('/products');
        $response->assertStatus(200);

        // 3. Visit cart page (empty)
        $response = $this->get('/cart');
        $response->assertStatus(200);

        // 4. Visit register page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 5. Register via API-like call (direct user creation)
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'dni' => '12345678',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        // 6. Login as user
        $this->actingAs($user);

        // 7. Visit cart as authenticated user
        $response = $this->get('/cart');
        $response->assertStatus(200);

        // 8. Logout - use withoutMiddleware to bypass CSRF
        $this->withoutMiddleware()->post('/logout');
    }

    public function test_exploratory_admin_flow(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'is_admin' => true,
        ]);

        // 1. Admin can access dashboard
        $response = $this->withoutMiddleware()->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // 2. Admin can view categories
        $response = $this->withoutMiddleware()->actingAs($admin)->get('/admin/categories');
        $response->assertStatus(200);

        // 3. Admin can create category
        $response = $this->withoutMiddleware()->actingAs($admin)->post('/admin/categories', [
            'name' => 'Test Category',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);

        // 4. Admin can update category
        $category = Category::first();
        $response = $this->withoutMiddleware()->actingAs($admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);

        // 5. Admin can delete category
        $response = $this->withoutMiddleware()->actingAs($admin)->delete("/admin/categories/{$category->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
