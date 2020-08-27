<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Cooperative;

class AdminConabControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_admins()
    {
        // Create fake users
        factory(User::class, 3)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($authenticatedUser, 'api')->getJson('/api/conab/admins');
        $response->assertOK()->assertJsonCount(3);
    }

    /** @test */
    public function should_return_only_admins()
    {
        // Create fake users
        factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        factory(User::class)->create(['user_type' => 'CUSTOMER']);
        factory(User::class)->create(['user_type' => 'ADMIN_COOP']);

        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($authenticatedUser, 'api')->getJson('/api/conab/admins');
        $response->assertOK()->assertJsonCount(1);
    }

    /** @test */
    public function should_return_an_empty_list_of_admins()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($authenticatedUser, 'api')->getJson('/api/conab/admins');
        $response->assertOK()->assertJsonCount(0);
    }

    /*
     * CREATE
     * name string,
     * email string,
     * phones string[],
     * cpf string
     * */

    /** @test */
    public function should_create_an_admin()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $data);
        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_name()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $dataWithoutName = [
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithoutName);
        $response->assertStatus(422);

        $dataWithInvalidName = [
            'name' => 123,
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];

        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithoutName);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_email()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $dataWithoutEmail = [
            'name' => 'any_name',
            'cpf' => '999.999.999-99',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithoutEmail);
        $response->assertStatus(422);

        $dataWithInvalidEmail = [
            'name' => 'any_name',
            'email' => 'invalidemail.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];

        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithInvalidEmail);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_cpf()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $dataWithoutCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithoutCpf);
        $response->assertStatus(422);

        $dataWithInvalidCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999/99', // invalid cpf
            'phones' => [
                '(99) 99999-9999',
                '(88) 88888-8888'
            ]
        ];

        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithInvalidCpf);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_phones()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);

        $dataWithoutPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => []
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithoutPhones);
        $response->assertStatus(422);

        $dataWithInvalidPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999/99',
            'phones' => '(99) 99999-9999' // must be an array
        ];

        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $dataWithInvalidPhones);
        $response->assertStatus(422);
    }

    /*
     * UPDATE
     * name string,
     * email string,
     * phones string[0],
     * cpf string
     * */

    /** @test */
    public function should_update_an_admin()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router PUT /api/conab/admins/:id with each data
        // Returns an admin updated
        // Assert status 200 and admin data
        $this->doesNotPerformAssertions();
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_incorrect_data()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router PUT /api/conab/admins/:id with each data
        // Throw an error
        // Assert status 400 and error name
        $this->doesNotPerformAssertions();
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_admin_does_not_exist()
    {
        // Only user authenticated
        // Don't create a fake admin
        // Request router PUT /api/conab/admins/:incorrect_id
        // Throw an error
        // Assert status 400 and error name
        $this->doesNotPerformAssertions();
    }

    /*
     * DETELE
     * Only conab's admins
     * */

    /** @test */
    public function should_delete_an_admin()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router DELETE /api/conab/admins/:id
        // Returns no content
        // Assert status 204 and database
        $this->doesNotPerformAssertions();
    }

    /** @test */
    public function on_the_delete_should_throw_an_error_if_admin_does_not_exist()
    {
        // Only user authenticated
        // Don't create a fake admin
        // Request router DELETE /api/conab/admins/:incorrect_id
        // Throw an error
        // Assert status 400 and error name
        $this->doesNotPerformAssertions();
    }
}
