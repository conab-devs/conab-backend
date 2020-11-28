<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;
use App\Phone;

/*Integration Tests For Routes Related with AdminConabController*/
class AdminConabControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_admins()
    {
        // Create fake users
        factory(User::class, 3)->create(['user_type' => 'ADMIN_CONAB']);
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins');
        $response->assertOK();
        $this->assertCount(3, $response['data']);
    }

    /** @test */
    public function should_return_un_authorized_if_customer_try_to_list_admins()
    {
        factory(User::class, 3)->create(['user_type' => 'ADMIN_CONAB']);
        $user = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins');
        $response->assertStatus(401);
    }


    /** @test */
    public function should_return_only_admins()
    {
        // Create fake users
        factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        factory(User::class)->create(['user_type' => 'CUSTOMER']);
        factory(User::class)->create(['user_type' => 'ADMIN_COOP']);

        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins');
        $response->assertOK();
        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function should_return_an_empty_list_of_admins()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins');
        $response->assertOK();
        $this->assertCount(0, $response['data']);
    }

    /** @test */
    public function should_paginate_each_5_admins()
    {
        // Create fake users
        factory(User::class, 6)->create(['user_type' => 'ADMIN_CONAB']);
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins?page=1');
        $response->assertOK();
        $this->assertCount(5, $response['data']);

        $response = $this->actingAs($user, 'api')->getJson('/api/conab/admins?page=2');
        $response->assertOK();
        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function should_return_one_admin()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $admin = factory(User::class)->create(['name' => 'admin', 'user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson("/api/conab/admins/$admin->id");
        $response->assertOK()->assertJsonFragment(['name' => 'admin']);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_show_an_admin()
    {
        $user = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $admin = factory(User::class)->create(['name' => 'admin', 'user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')->getJson("/api/conab/admins/$admin->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_create_an_admin()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $data);
        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_create_an_admin()
    {
        $authenticatedUser = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson('/api/conab/admins', $data);
        $response->assertStatus(401);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_name()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutName = [
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithoutName);
        $response->assertStatus(422);

        $dataWithInvalidName = [
            'name' => 123,
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithInvalidName);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_email()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutEmail = [
            'name' => 'any_name',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithoutEmail);
        $response->assertStatus(422);

        $dataWithInvalidEmail = [
            'name' => 'any_name',
            'email' => 'invalidemail.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithInvalidEmail);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_cpf()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithoutCpf);
        $response->assertStatus(422);

        $dataWithInvalidCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999/99', // invalid cpf
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithInvalidCpf);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_invalid_phones()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => []
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithoutPhones);
        $response->assertStatus(422);

        $dataWithInvalidPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => '(99) 99999-9999' // must be an array
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithInvalidPhones);
        $response->assertStatus(422);

        $dataWithInvalidPhoneNumbers = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '9999A99999' ],
                [ 'number' => '(84) 99999999' ]
            ]
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithInvalidPhoneNumbers);
        $response->assertStatus(422);

        $dataWithSamePhoneNumbers = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(99) 99999-9999' ]
            ]
        ];

        $response = $authenticatedRoute->postJson('/api/conab/admins', $dataWithSamePhoneNumbers);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_an_existing_cpf()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        factory(User::class)->create(['cpf' => '111.111.111-11', 'user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11', // same cpf
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_an_existing_phone()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $admin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11',
            'phones' => [
                [ 'number' => $admin->phones[0]->number ], // existing phone
            ]
        ];
        $response = $authenticatedRoute->postJson('/api/conab/admins', $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_an_admin()
    {
        $user = factory(User::class)
            ->create(['password' => '123456', 'user_type' => 'ADMIN_CONAB']);

        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithOnlyName = ['name' => 'updated_name'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyName);
        $response->assertOk()->assertJsonFragment(['name' => $dataWithOnlyName['name']]);

        $dataWithOnlyEmail = ['email' => 'updated@email.com'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyEmail);
        $response->assertOk()->assertJsonFragment(['email' => $dataWithOnlyEmail['email']]);

        $dataWithOnlyCpf = ['cpf' => '111.111.111-11'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyCpf);
        $response->assertOk()->assertJsonFragment(['cpf' => $dataWithOnlyCpf['cpf']]);

        $dataWithOnlyPassword = [
            'password' => '123456', // current password
            'new_password' => '654321'
        ];

        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyPassword);
        $response->assertOk();

        $dataWithOnlyPhones = [
            'phones' => [
                [ 'number' => '(11) 11111-1111' ],
                [ 'number' => '(22) 22222-2222' ]
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyPhones);
        $response->assertOk();
        $this->assertDatabaseHas('phones', ['number' => '(11) 11111-1111']);
        $this->assertDatabaseHas('phones', ['number' => '(22) 22222-2222']);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_update_an_admin()
    {
        $user = factory(User::class)
            ->create(['password' => '123456', 'user_type' => 'CUSTOMER']);

        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithOnlyName = ['name' => 'updated_name'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyName);
        $response->assertStatus(401);

        $dataWithOnlyEmail = ['email' => 'updated@email.com'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyEmail);
        $response->assertStatus(401);

        $dataWithOnlyCpf = ['cpf' => '111.111.111-11'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyCpf);
        $response->assertStatus(401);

        $dataWithOnlyPassword = [
            'password' => '123456', // current password
            'new_password' => '654321'
        ];

        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyPassword);
        $response->assertStatus(401);

        $dataWithOnlyPhones = [
            'phones' => [
                [ 'number' => '(11) 11111-1111' ],
                [ 'number' => '(22) 22222-2222' ]
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithOnlyPhones);
        $response->assertStatus(401);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_invalid_name()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidName = ['name' => 123];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidName);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_invalid_email()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidEmailAsANumber = ['email' => 123];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidEmailAsANumber);
        $response->assertStatus(422);

        $dataWithInvalidEmail = ['email' => 'invalidemail.com'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidEmail);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_invalid_cpf()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidCpfAsANumber = ['cpf' => 123];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidCpfAsANumber);
        $response->assertStatus(422);

        $dataWithInvalidCpf = ['cpf' => '99A.999.999/60'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidCpf);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_invalid_password()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidPasswordAsANumber = ['password' => 123, 'new_password' => 'any_password'];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidPasswordAsANumber);
        $response->assertStatus(422);

        $dataWithInvalidNewPasswordAsANumber = ['password' => 'any_password', 'new_password' => 123];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidNewPasswordAsANumber);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_invalid_phones()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidPhoneNumbers = [
            'phones' => [
                [ 'number' => '9999A99999' ],
                [ 'number' => '(84) 99999999' ]
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithInvalidPhoneNumbers);
        $response->assertStatus(422);

        $dataWithSamePhoneNumbers = [
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(99) 99999-9999' ]
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $dataWithSamePhoneNumbers);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_an_existing_cpf()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        factory(User::class)->create(['cpf' => '111.111.111-11', 'user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11', // same cpf
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_update_should_throw_an_error_if_pass_an_existing_phone()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $phone = factory(Phone::class)->create();
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11',
            'phones' => [
                [ 'number' => $phone->number ], // existing phone
            ]
        ];
        $response = $authenticatedRoute->putJson("/api/conab/admins", $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_delete_an_admin()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $fakeAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $authenticatedRoute->deleteJson("/api/users/$fakeAdmin->id");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $fakeAdmin->id]);
    }

    /** @test */
    public function should_return_unauthorized_if_admin_conab_try_to_delete_customer()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $customer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $response = $authenticatedRoute->deleteJson("/api/users/$customer->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_delete_an_admin_conab()
    {
        $user = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $fakeAdmin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $authenticatedRoute->deleteJson("/api/users/$fakeAdmin->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_destroy_admin_coop()
    {
        $customer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $admin = factory(User::class)->create(['user_type' => 'ADMIN_COOP']);
        $authenticatedRoute = $this->actingAs($customer, 'api');
        $response = $authenticatedRoute->deleteJson("/api/users/$admin->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_unauthorized_if_customer_try_to_destroy_other_customer_account()
    {
        $customer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $anotherCustomer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $authenticatedRoute = $this->actingAs($customer, 'api');
        $response = $authenticatedRoute->deleteJson("/api/users/$anotherCustomer->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function on_the_delete_should_throw_an_error_if_admin_does_not_exist()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $fakeId = 10;
        $response = $authenticatedRoute->deleteJson("/api/users/$fakeId");
        $response->assertStatus(404);
    }
}
