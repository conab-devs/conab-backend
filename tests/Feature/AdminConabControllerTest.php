<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;

class AdminConabControllerTest extends TestCase
{
    use RefreshDatabase;

    /*
     * LIST
     * List only conab's admin users
     * Returns name, email, phone[0] and cpf
     * */

    /* @test */
    public function ShouldReturnAdmins()
    {
        // Create fake admins
        factory(User::class, 3)->create([
            'user_type' => 'ADMIN_CONAB'
        ]);
        $authenticatedUser = factory(User::class)->create([
            'user_type' => 'ADMIN_CONAB'
        ]);
        $response = $this->actingAs($authenticatedUser, 'api')->getJson('/api/conab/admins');
        $response->assertOK()->assertJsonCount(3);
    }

    /* @test */
    public function ShouldReturnOnlyAdmins()
    {
        // Create fake admins
        factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        factory(User::class)->create(['user_type' => 'COSTUMER']);
        factory(User::class)->create(['user_type' => 'ADMIN_COOP']);

        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($authenticatedUser, 'api')->getJson('/api/conab/admins');
        $response->assertOK()->assertJsonCount(1);
    }

    /* @test */
    public function ShouldReturnAnEmptyListOfAdmins()
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

    /* @test */
    public function ShouldCreateAnAdmin()
    {
        // Only user authenticated
        // Request router POST /api/conab/admins with data
        // Return an admin
        // Assert status 201 and admin data
    }

    /* @test */
    public function OnTheCreationShouldThrowAnErrorIfPassIncorrectData()
    {
        // Only user authenticated
        // Request router POST /api/conab/admins with each incorrect data
        // Throw an error
        // Assert status 400 and error name
    }

    /*
     * UPDATE
     * name string,
     * email string,
     * phones string[0],
     * cpf string
     * */

    /* @test */
    public function ShouldUpdateAnAdmin()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router PUT /api/conab/admins/:id with each data
        // Returns an admin updated
        // Assert status 200 and admin data
    }

    /* @test */
    public function OnTheUpdateShouldThrowAnErrorIfPassIncorrectData()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router PUT /api/conab/admins/:id with each data
        // Throw an error
        // Assert status 400 and error name
    }

        /* @test */
    public function OnTheUpdateShouldThrowAnErrorIfAdminDoesNotExist()
    {
        // Only user authenticated
        // Don't create a fake admin
        // Request router PUT /api/conab/admins/:incorrect_id
        // Throw an error
        // Assert status 400 and error name
    }

    /*
     * DETELE
     * Only conab's admins
     * */

    /* @test */
    public function ShouldDeleteAnAdmin()
    {
        // Only user authenticated
        // Create a fake admin
        // Request router DELETE /api/conab/admins/:id
        // Returns no content
        // Assert status 204 and database
    }

       /* @test */
    public function OnTheDeleteShouldThrowAnErrorIfAdminDoesNotExist()
    {
        // Only user authenticated
        // Don't create a fake admin
        // Request router DELETE /api/conab/admins/:incorrect_id
        // Throw an error
        // Assert status 400 and error name
    }
}
