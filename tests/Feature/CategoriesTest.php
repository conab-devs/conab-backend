<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'description' => 'This is a valid description about the category.'
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_name_is_passed()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 123456,
            'description' => 'This is a valid description about the category.'
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_description_is_passed()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 123456
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_create_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 'valid_description'
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'valid_name'
        ]);
    }

    /** @test */
    public function should_return_created_category()
    {
        $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $response = $this->actingAs($user, 'api')->postJson('api/categories', [
            'name' => 'valid_name',
            'description' => 'valid_description'
        ]);

        $response->assertCreated()->assertJson([
            'name' => 'valid_name',
            'description' => 'valid_description'
        ]);
    }

    /** @test */
    public function should_return_five_categories()
    {
      $user = factory(\App\User::class)->create(['user_type' => 'ADMIN_CONAB']);

      factory(\App\Category::class, 5)->create();

      $response = $this->actingAs($user, 'api')->getJson('api/categories');

      $response->assertOk()->assertJsonCount(5);
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
            'description' => 'valid_description'
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
                'description' => 'This is a valid description about the category.'
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
                'description' => 123456
            ]);

        $response->assertStatus(422);
    }
}
