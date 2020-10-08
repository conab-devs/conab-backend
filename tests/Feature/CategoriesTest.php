<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'description' => 'This is a valid description about the category.',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_name_is_passed_to_store()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 123456,
            'description' => 'This is a valid description about the category.',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_description_is_passed_to_store()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 123456,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_create_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 'valid_description',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'valid_name',
        ]);
    }

    /** @test */
    public function should_return_created_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 'valid_description',
        ]);

        $response->assertCreated()->assertJson([
            'name' => 'valid_name',
            'description' => 'valid_description',
        ]);
    }

    /** @test */
    public function should_return_five_categories()
    {
        factory(\App\Category::class, 5)->create();

        $conabAdmin = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $conabAdminResponse = $this->actingAs($conabAdmin, 'api')
            ->getJson('api/categories');
        $conabAdminResponse->assertOk()->assertJsonCount(5);

        $customer = factory(\App\User::class)->create(['user_type' => 'CUSTOMER']);
        $customerResponse = $this->actingAs($customer, 'api')
            ->getJson('api/categories');
        $customerResponse->assertOk()->assertJsonCount(5);

        $cooperativeAdmin = factory(\App\User::class)->create(['user_type' => 'ADMIN_COOP']);
        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->getJson('api/categories');
        $cooperativeAdminResponse->assertOk()->assertJsonCount(5);
    }

    /** @test */
    public function should_return_one_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $category = factory(\App\Category::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson("api/categories/$category->id");

        $response->assertOk()->assertJson($category->toArray());
    }

    /** @test */
    public function should_update_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $category = factory(\App\Category::class)->create();

        $newAttributes = [
            'name' => 'valid_name',
            'description' => 'valid_description',
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson("api/categories/$category->id", $newAttributes);

        $response->assertOk()->assertJson($newAttributes);
    }

    /** @test */
    public function should_return_validation_error_if_integer_name_is_passed_to_update()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $category = factory(\App\Category::class)->create();

        $response = $this->actingAs($user, 'api')
            ->putJson("api/categories/$category->id", [
                'name' => 123456,
                'description' => 'This is a valid description about the category.',
            ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_description_is_passed_to_update()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $category = factory(\App\Category::class)->create();

        $response = $this->actingAs($user, 'api')
            ->putJson("api/categories/$category->id", [
                'name' => 'valid_name',
                'description' => 123456,
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function should_delete_an_category_and_return_response_without_content()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $category = factory(\App\Category::class)->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson("api/categories/$category->id");

        $response->assertNoContent();

        $this->assertDeleted('categories', $category->toArray());
    }

    /** @test */
    public function only_conab_admins_should_be_able_to_delete_an_category()
    {
        $customer = factory(\App\User::class)->create(['user_type' => 'CUSTOMER']);
        $category = factory(\App\Category::class)->create();
        $customerResponse = $this->actingAs($customer, 'api')
            ->deleteJson("api/categories/$category->id");
        $customerResponse->assertUnauthorized();

        $cooperativeAdmin = factory(\App\User::class)->create(['user_type' => 'ADMIN_COOP']);
        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->deleteJson("api/categories/$category->id");
        $cooperativeAdminResponse->assertUnauthorized();
    }

    /** @test */
    public function only_conab_admins_should_be_able_to_update_an_category()
    {
        $newAttributes = [
            'name' => 'valid_name',
            'description' => 'valid_description',
        ];

        $customer = factory(\App\User::class)->create(['user_type' => 'CUSTOMER']);
        $category = factory(\App\Category::class)->create();
        $customerResponse = $this->actingAs($customer, 'api')
            ->putJson("api/categories/$category->id", $newAttributes);
        $customerResponse->assertUnauthorized();

        $cooperativeAdmin = factory(\App\User::class)->create(['user_type' => 'ADMIN_COOP']);
        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->putJson("api/categories/$category->id", $newAttributes);
        $cooperativeAdminResponse->assertUnauthorized();
    }

    /** @test */
    public function only_conab_admins_should_be_able_to_store_an_category()
    {
        $attributes = [
            'name' => 'valid_name',
            'description' => 'valid_description',
        ];

        $customer = factory(\App\User::class)->create(['user_type' => 'CUSTOMER']);
        $customerResponse = $this->actingAs($customer, 'api')
            ->postJson("api/categories", $attributes);
        $customerResponse->assertUnauthorized();

        $cooperativeAdmin = factory(\App\User::class)->create(['user_type' => 'ADMIN_COOP']);
        $cooperativeAdminResponse = $this->actingAs($cooperativeAdmin, 'api')
            ->postJson("api/categories", $attributes);
        $cooperativeAdminResponse->assertUnauthorized();
    }
}
