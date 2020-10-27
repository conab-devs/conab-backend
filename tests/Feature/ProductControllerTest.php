<?php

namespace Tests\Feature;

use App\Category;
use App\Cooperative;
use App\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_create_an_product()
    {
        $cooperative = factory(Cooperative::class)->create();
        $cooperativeAdmin = factory(User::class)->create([
            'cooperative_id' => $cooperative->id,
            'user_type' => 'ADMIN_COOP'
        ]);
        $category = factory(Category::class)->create();

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => $category->id
        ];

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    /** @test */
    public function should_get_product_by_id()
    {
        $cooperative = factory(Cooperative::class)->create();
        $cooperativeAdmin = factory(User::class)->create([
            'cooperative_id' => $cooperative->id,
            'user_type' => 'ADMIN_COOP'
        ]);
        $category = factory(Category::class)->create();
        $product = factory(Product::class)->create([
            'cooperative_id' => $cooperative->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->get("/api/products/$product->id");

        $response->assertOk()->assertJson($product->toArray());
    }

    /** @test */
    public function should_deny_product_create_to_users_who_are_not_cooperative_administrators()
    {
        $conabAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $category = factory(Category::class)->create();

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => $category->id
        ];

        $response = $this->actingAs($conabAdmin, 'api')
            ->postJson('/api/products', $data);

        $response->assertStatus(401);
    }
}
