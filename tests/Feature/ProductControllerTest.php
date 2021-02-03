<?php

namespace Tests\Feature;

use App\Category;
use App\Cooperative;
use App\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** @group products */
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

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'unit_of_measure' => 'kg',
            'available' => true,
            'category_id' => factory(Category::class)->create()->id
        ];

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $response->assertJson([
            "cooperative" => $cooperative->toArray()
        ]);
    }

    /** @test */
    public function should_get_product_by_id()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create();

        $product = factory(Product::class)->create([
            'cooperative_id' => $cooperative->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/products/$product->id");

        $expectedProductStructure = array_keys($product->toArray());
        array_push($expectedProductStructure, 'cooperative');
        array_push($expectedProductStructure, 'category');
        $response->assertOk()->assertJson($product->toArray());
        $response->assertJsonStructure($expectedProductStructure);
    }

    /** @test */
    public function should_deny_product_create_to_users_who_are_not_cooperative_administrators()
    {
        $conabAdmin = factory(User::class)->create([
            'user_type' => 'ADMIN_CONAB',
            'cooperative_id' => null
        ]);
        $costumer = factory(User::class)->create([
            'user_type' => 'CUSTOMER',
            'cooperative_id' => null
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'unit_of_measure' => 'kg',
            'category_id' => factory(Category::class)->create()->id
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

        $cooperative = factory(Cooperative::class)->create();

        $product = factory(Product::class)->create([
            'cooperative_id' => $cooperative->id
        ]);

        $conabAdminResponse = $this->actingAs($conabAdmin, 'api')
            ->deleteJson("/api/products/$product->id");
        $conabAdminResponse->assertStatus(401);

        $costumerResponse = $this->actingAs($costumer, 'api')
            ->deleteJson("/api/products/$product->id");
        $costumerResponse->assertStatus(401);
    }

    /** @test */
    public function should_allow_product_delete_to_users_who_are_cooperative_administrators()
    {
        $cooperative = factory(Cooperative::class)->create();

        $cooperativeAdmin = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative->id
        ]);

        $product = factory(Product::class)->create([
            'cooperative_id' => $cooperative->id
        ]);

        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->deleteJson("/api/products/$product->id");

        $cooperativeAdminResponse->assertStatus(204);
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
            'cooperative_id' => $cooperative->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'unit_of_measure' => 'kg',
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
            'cooperative_id' => factory(Cooperative::class)->create()->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'unit_of_measure' => 'kg',
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
            'cooperative_id' => $cooperative1->id
        ]);

        $cooperative2 = factory(Cooperative::class)->create();
        $cooperativeAdmin2 = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative2->id
        ]);

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'unit_of_measure' => 'kg',
            'category_id' => factory(Category::class)->create()->id
        ];

        $response = $this->actingAs($cooperativeAdmin2, 'api')
            ->putJson("/api/products/$product->id", $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_a_list_products()
    {
        $costumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperatives = factory(Cooperative::class, 3)->create();

        $cooperativesIds = array_map(function ($cooperative) {
            return $cooperative['id'];
        }, $cooperatives->toArray());

        factory(Product::class, 400)->create([
            'cooperative_id' => $cooperativesIds[rand(0, 2)]
        ]);

        $response = $this->actingAs($costumer, 'api')
            ->getJson('/api/products');

        $response->assertOk();
        $this->assertCount(100, $response['data']);
    }

    /** @test */
    public function should_return_a_filtered_list_of_products_by_cooperative()
    {
        $cooperative = factory(Cooperative::class)->create();

        $amountProduct = 40;

        factory(Product::class, $amountProduct)->create([
            'cooperative_id' => $cooperative->id
        ]);

        factory(Product::class, 20)->create([
            'cooperative_id' => factory(Cooperative::class)->create()->id
        ]);

        $cooperativeAdmin = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
            'cooperative_id' => $cooperative->id
        ]);

        $response = $this->actingAs($cooperativeAdmin, 'api')
            ->getJson("/api/products?cooperative=$cooperative->id");

        $response->assertOk();
        $this->assertCount($amountProduct, $response['data']);
    }

    /** @test */
    public function should_return_a_filtered_list_of_products_by_name()
    {
        $consumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);

        $names = ['Tomate', 'Mel', 'Leite de Cabra', 'Bolo de Ovos', 'Caramelo', 'Leite de Vaca'];

        foreach ($names as $name) {
            factory(Product::class)->create([
                'name' => $name,
                'cooperative_id' => factory(Cooperative::class)->create()->id
            ]);
        }

        $response = $this->actingAs($consumer, 'api')
            ->getJson("/api/products?name=ca");
        $response->assertOk();
        $this->assertCount(3, $response['data']);

        $response = $this->actingAs($consumer, 'api')
            ->getJson("/api/products?name=de");
        $response->assertOk();
        $this->assertCount(3, $response['data']);

        $response = $this->actingAs($consumer, 'api')
            ->getJson("/api/products?name=Leite%20de");
        $response->assertOk();
        $this->assertCount(2, $response['data']);
    }

    /** @test */
    public function should_return_a_filtered_list_of_products_by_category()
    {
        $consumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperative = factory(Cooperative::class)->create();

        $categories = factory(Category::class, 3)->create()->toArray();

        factory(Product::class, 40)->create([
            'category_id' => $categories[0]['id'],
            'cooperative_id' => $cooperative->id
        ]);

        factory(Product::class, 70)->create([
            'category_id' => $categories[1]['id'],
            'cooperative_id' => $cooperative->id
        ]);

        $category = $categories[0]['id'];
        $response = $this->actingAs($consumer, 'api')
            ->getJson("/api/products?category=$category");
        $response->assertOk();

        $this->assertCount(40, $response['data']);

        $category = $categories[1]['id'];
        $response = $this->actingAs($consumer, 'api')
            ->getJson("/api/products?category=$category");
        $response->assertOk();

        $this->assertCount(70, $response['data']);
    }

    /** @test */
    public function should_return_a_list_of_products_in_desc_order()
    {
        $consumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperative = factory(Cooperative::class)->create()->id;
        $category = factory(Category::class)->create()->id;

        $products = factory(Product::class, 20)->create([
            'category_id' => $category,
            'cooperative_id' => $cooperative
        ]);

        $sorted = $products->sortByDesc(function ($product, $key) {
            return $product->price;
        })->values();

        $response = $this->actingAs($consumer, 'api')
            ->getJson('/api/products?order=desc');

        $response->assertOk();
        foreach ($response['data'] as $key => $product) {
            $this->assertEquals($sorted[$key]->price, $product['price']);
        }
    }

    /** @test */
    public function should_return_a_list_of_products_in_asc_order()
    {
        $consumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperative = factory(Cooperative::class)->create()->id;
        $category = factory(Category::class)->create()->id;

        $products = factory(Product::class, 20)->create([
            'category_id' => $category,
            'cooperative_id' => $cooperative
        ]);

        $sorted = $products->sortBy(function ($product, $key) {
            return $product->price;
        })->values();

        $response = $this->actingAs($consumer, 'api')
            ->getJson('/api/products?order=asc');

        $response->assertOk();
        foreach ($response['data'] as $key => $product) {
            $this->assertEquals($sorted[$key]->price, $product['price']);
        }
    }

    /** @test */
    public function should_return_a_filtered_list_of_products_by_price_range()
    {
        $consumer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $cooperative = factory(Cooperative::class)->create()->id;
        $category = factory(Category::class)->create()->id;

        factory(Product::class, 40)->create([
            'category_id' => $category,
            'cooperative_id' => $cooperative
        ]);

        $response = $this->actingAs($consumer, 'api')
            ->getJson('/api/products?min_price=40&max_price=70');

        $response->assertOk();
        foreach ($response['data'] as $product) {
            $this->assertTrue($product['price'] >= 40 && $product['price'] <= 70);
        }

        $response = $this->actingAs($consumer, 'api')
            ->getJson('/api/products?min_price=80');

        $response->assertOk();
        foreach ($response['data'] as $product) {
            $this->assertTrue($product['price'] >= 80);
        }

        $response = $this->actingAs($consumer, 'api')
            ->getJson('/api/products?max_price=30');

        $response->assertOk();
        foreach ($response['data'] as $product) {
            $this->assertTrue($product['price'] <= 30);
        }
    }
}
