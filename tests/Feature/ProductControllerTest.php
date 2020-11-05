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
        $user = factory(User::class)->create();

        $category = factory(Category::class)->create();
        $product = factory(Product::class)->create([
            'cooperative_id' => $cooperative->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user, 'api')
            ->get("/api/products/$product->id");

        $response->assertOk()->assertJson($product->toArray());
    }

    /** @test */
    public function should_deny_product_create_to_users_who_are_not_cooperative_administrators()
    {
        $conabAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $costumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $category = factory(Category::class)->create();

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => $category->id
        ];

        $conabAdminResponse = $this->actingAs($conabAdmin, 'api')
            ->postJson('/api/products', $data);
        $conabAdminResponse->assertStatus(401);

        $costumerResponse = $this->actingAs($costumer, 'api')
            ->postJson('/api/products', $data);
        $costumerResponse->assertStatus(401);
    }

    /** @test */
    public function should_deny_product_delete_to_users_who_are_not_cooperative_administrators()
    {
        $conabAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $costumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);

        $category = factory(Category::class)->create();
        $cooperative = factory(Cooperative::class)->create();

        $product = factory(Product::class)->create([
            'category_id' => $category->id,
            'cooperative_id' => $cooperative->id
        ]);

        $conabAdminResponse = $this->actingAs($conabAdmin, 'api')
            ->delete("/api/products/$product->id");
        $conabAdminResponse->assertStatus(401);

        $costumerResponse = $this->actingAs($costumer, 'api')
            ->delete("/api/products/$product->id");
        $costumerResponse->assertStatus(401);
    }

    /** @test */
    public function should_allow_product_delete_to_users_who_are_cooperative_administrators()
    {
        $cooperative = factory(Cooperative::class)->create();
        $category = factory(Category::class)->create();

        $cooperativeAdmin = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative->id
        ]);

        $product = factory(Product::class)->create([
            'category_id' => $category->id,
            'cooperative_id' => $cooperative->id
        ]);

        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->delete("/api/products/$product->id");

        $cooperativeAdminResponse->assertStatus(200);
    }

    /** @test */
    public function should_update_a_product()
    {
        $cooperative = factory(Cooperative::class)->create();
        $cooperativeAdmin = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative->id
        ]);

        $product = factory(Product::class)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => $cooperative->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => factory(Category::class)->create()->id
        ];

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->putJson("/api/products/$product->id", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);
    }

    /** @test */
    public function should_deny_that_users_who_are_not_cooperative_administrators_update_a_product()
    {
        $conabAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $costumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);

        $product = factory(Product::class)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => factory(Cooperative::class)->create()->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => factory(Category::class)->create()->id
        ];

        $conabAdminResponse = $this->actingAs($conabAdmin, 'api')
            ->putJson("/api/products/$product->id", $data);
        $conabAdminResponse->assertStatus(401);

        $costumerResponse = $this->actingAs($costumer, 'api')
            ->putJson("/api/products/$product->id", $data);
        $costumerResponse->assertStatus(401);
    }

    /** @test */
    public function should_deny_that_cooperative_administrators_update_products_that_are_not_their_own()
    {
        $cooperative1 = factory(Cooperative::class)->create();

        $product = factory(Product::class)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => $cooperative1->id
        ]);

        $cooperative2 = factory(Cooperative::class)->create();
        $cooperative2Admin = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative2->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => factory(Category::class)->create()->id
        ];

        $response = $this->actingAs($cooperative2Admin, 'api')
            ->putJson("/api/products/$product->id", $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_a_list_products()
    {
        $costumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperatives = factory(Cooperative::class, 3)->create();

        $idCoopeartives = array_map(function ($cooperative) {
            return $cooperative['id'];
        }, $cooperatives->toArray());

        $products = factory(Product::class, 20)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => $idCoopeartives[rand(0, 2)]
        ]);

        $response = $this->actingAs($costumer, 'api')
            ->getJson('/api/products');

        $products = array_slice($products->toArray(), 0, 5);
        $response->assertOk();
        $this->assertCount(5, $response['data']);
    }

    /** @test */
    public function should_return_products_from_their_own_cooperative()
    {
        $cooperative = factory(Cooperative::class)->create();

        $amountProduct = 3;

        factory(Product::class, $amountProduct)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => $cooperative->id
        ])->toArray();

        factory(Product::class, 7)->create([
            'category_id' => factory(Category::class)->create()->id,
            'cooperative_id' => factory(Cooperative::class)->create()->id
        ]);

        $cooperativeAdmin = factory(User::class)->create([
            'cooperative_id' => $cooperative->id,
            'user_type' => 'ADMIN_COOP'
        ]);

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->getJson("/api/products/cooperative/$cooperative->id");

        $response->assertOk();
        $this->assertCount($amountProduct, $response['data']);
    }
}
