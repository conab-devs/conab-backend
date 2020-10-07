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
}
