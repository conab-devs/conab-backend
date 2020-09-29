<?php

namespace Tests\Feature;

use App\Cooperative;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Phone;

class AdminCooperativeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_all_cooperative_admins_paginated()
    {
        $cooperative = factory(Cooperative::class)->create();
        $cooperative->admins()->createMany(
            factory(User::class, 3)
            ->make(['user_type' => 'ADMIN_COOP'])
            ->each(function ($model) {
                $model->makeVisible(['password']);
            })
            ->toArray()
        );
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins");
        $response->assertOK();
        $this->assertCount(3, $response['data']);

        $cooperative->admins()->createMany(
            factory(User::class, 3)
            ->make(['user_type' => 'ADMIN_COOP'])
            ->each(function ($model) {
                $model->makeVisible(['password']);
            })
            ->toArray()
        );

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins");
        $response->assertOK();
        $this->assertCount(5, $response['data']);
    }

    /** @test */
    public function should_return_unauthorized_if_user_is_not_a_conab_admin_on_index()
    {
        $cooperative = factory(Cooperative::class)->create();
        $cooperative->admins()->createMany(
            factory(User::class, 3)
            ->make(['user_type' => 'ADMIN_COOP'])
            ->each(function ($model) {
                $model->makeVisible(['password']);
            })
            ->toArray()
        );
        $user = factory(User::class)->create(['user_type' => 'ADMIN_COOP']);
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_not_found_if_the_sought_admin_does_not_exists()
    {
        $cooperative = factory(Cooperative::class)->create();

        $secondCooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make(['user_type' => 'ADMIN_COOP']);
        $secondCooperative->admins()->save($admin);
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins/$admin->id");
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_conab_should_get_the_right_admin_based_on_his_id()
    {
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make(['user_type' => 'ADMIN_COOP']);
        $cooperative->admins()->save($admin);
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins/$admin->id");
        $response->assertOK();
        $response->assertJson($admin->toArray());
    }

    /** @test */
    public function admin_cooperative_should_get_his_information_by_id()
    {
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make(['user_type' => 'ADMIN_COOP']);
        $cooperative->admins()->save($admin);
        $admin->makeHidden('cooperative');
        $response = $this->actingAs($admin, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins/$admin->id");
        $response->assertOK();
        $response->assertJson($admin->toArray());
    }

    /** @test */
    public function should_return_unauthorized_on_show_if_admin_cooperative_try_to_show_others_informations()
    {
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make(['user_type' => 'ADMIN_COOP']);
        $cooperative->admins()->save($admin);
        $user = factory(User::class)->create([
            'user_type' => 'ADMIN_COOP',
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/cooperatives/$cooperative->id/admins/$admin->id");
        $response->assertStatus(401);
    }

    /** @test */
    public function should_create_an_admin_cooperative()
    {
        $cooperative = factory(Cooperative::class)->create();
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson("/api/cooperatives/$cooperative->id/admins", $data);
        $response->assertStatus(201)
            ->assertJson($data);
        $this->assertDatabaseHas('users', [
            'cooperative_id' => $cooperative->id,
        ]);
    }

    /** @test */
    public function should_return_unauthorized_if_user_is_not_a_conab_admin_on_store()
    {
        $cooperative = factory(Cooperative::class)->create();
        $authenticatedUser = factory(User::class)->create(['user_type' => 'ADMIN_COOP']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $this->actingAs($authenticatedUser, 'api')
            ->postJson("/api/cooperatives/$cooperative->id/admins", $data);
        $response->assertStatus(401);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_name_is_passed()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutName = [
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithoutName);
        $response->assertStatus(422);

        $dataWithInvalidName = [
            'name' => 123,
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];

        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithInvalidName);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_email_is_passed()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutEmail = [
            'name' => 'any_name',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithoutEmail);
        $response->assertStatus(422);

        $dataWithInvalidEmail = [
            'name' => 'any_name',
            'email' => 'invalidemail.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];

        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithInvalidEmail);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_cpf_is_passed()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithoutCpf);
        $response->assertStatus(422);

        $dataWithInvalidCpf = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999/99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];

        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithInvalidCpf);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_phones_are_passed()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithoutPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'phones' => [],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithoutPhones);
        $response->assertStatus(422);

        $dataWithInvalidPhones = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => '(99) 99999-9999',
        ];

        $response = $authenticatedRoute->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithInvalidPhones);
        $response->assertStatus(422);

        $dataWithInvalidPhoneNumbers = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '9999A99999'],
                ['number' => '(84) 99999999'],
            ],
        ];

        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithInvalidPhoneNumbers);
        $response->assertStatus(422);

        $dataWithSamePhoneNumbers = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '999.999.999-99',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(99) 99999-9999'],
            ],
        ];

        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $dataWithSamePhoneNumbers);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_an_existing_cpf_is_passed()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        factory(User::class)->create([
            'cpf' => '111.111.111-11',
            'user_type' => 'ADMIN_CONAB'
        ]);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11',
            'phones' => [
                ['number' => '(99) 99999-9999'],
                ['number' => '(88) 88888-8888'],
            ],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function on_the_creation_should_throw_an_error_if_pass_an_existing_phone()
    {
        $cooperative = factory(Cooperative::class)->create();
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        $admin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11',
            'phones' => [
                ['number' => $admin->phones[0]->number],
            ],
        ];
        $response = $authenticatedRoute
            ->postJson("/api/cooperatives/$cooperative->id/admins", $data);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_delete_an_cooperative_admin()
    {
        $admin = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($admin, 'api');
        $user = factory(User::class)->make(['user_type' => 'ADMIN_COOP']);
        $cooperative = factory(\App\Cooperative::class)->create();
        $cooperative->admins()->save($user);
        $response = $authenticatedRoute->deleteJson("/api/users/$user->id");
        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function should_return_unauthorized_if_admin_coop_try_to_destroy_someones_account()
    {
        $customer = factory(User::class)->create(['user_type' => 'CUSTOMER']);
        $admin = factory(User::class)->create(['user_type' => 'ADMIN_COOP']);
        $authenticatedRoute = $this->actingAs($admin, 'api');
        $response = $authenticatedRoute->deleteJson("/api/users/$customer->id");
        $response->assertStatus(401);

        $adminConab = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $adminConabDestroyResponse = $authenticatedRoute->deleteJson("/api/users/$adminConab->id");
        $adminConabDestroyResponse->assertStatus(401);

        $adminCoop = factory(User::class)->create(['user_type' => 'ADMIN_COOP']);
        $adminCoopDestroyResponse = $authenticatedRoute->deleteJson("/api/users/$adminCoop->id");
        $adminCoopDestroyResponse->assertStatus(401);
    }

    /** @test */
    public function should_throw_an_error_if_admin_does_not_exists()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');
        $fakeId = 10;
        $response = $authenticatedRoute->deleteJson("/api/conab/admins/$fakeId");
        $response->assertStatus(404);
    }

    /** @test */
    public function should_update_an_admin()
    {
        $user = factory(User::class)
            ->create(['password' => '123456', 'user_type' => 'ADMIN_CONAB']);

        $authenticatedRoute = $this->actingAs($user, 'api');

        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $dataWithOnlyName = ['name' => 'updated_name'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithOnlyName
        );
        $response->assertOk()->assertJsonFragment(['name' => $dataWithOnlyName['name']]);

        $dataWithOnlyEmail = ['email' => 'updated@email.com'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id", $dataWithOnlyEmail
        );
        $response->assertOk()->assertJsonFragment(['email' => $dataWithOnlyEmail['email']]);

        $dataWithOnlyCpf = ['cpf' => '111.111.111-11'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithOnlyCpf
        );
        $response->assertOk()->assertJsonFragment(['cpf' => $dataWithOnlyCpf['cpf']]);

        $dataWithOnlyPassword = [
            'password' => '123456',
            'new_password' => '654321'
        ];

        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithOnlyPassword
        );
        $response->assertOk();

        $dataWithOnlyPhones = [
            'phones' => [
                [ 'number' => '(11) 11111-1111' ],
                [ 'number' => '(22) 22222-2222' ]
            ]
        ];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithOnlyPhones
        );
        $response->assertOk();
        $this->assertDatabaseHas('phones', ['number' => '(11) 11111-1111']);
        $this->assertDatabaseHas('phones', ['number' => '(22) 22222-2222']);
    }

    /** @test */
    public function should_return_unauthorized_if_user_is_not_the_resource_owner_on_update()
    {
        $user = factory(User::class)
            ->create(['password' => '123456', 'user_type' => 'ADMIN_COOP']);

        $authenticatedRoute = $this->actingAs($user, 'api');

        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $dataWithOnlyName = ['name' => 'updated_name'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithOnlyName
        );
        $response->assertStatus(401);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_name_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidName = ['name' => 123];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidName
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_email_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidEmailAsANumber = ['email' => 123];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidEmailAsANumber
        );
        $response->assertStatus(422);

        $dataWithInvalidEmail = ['email' => 'invalidemail.com'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidEmail
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_cpf_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidCpfAsANumber = ['cpf' => 123];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidCpfAsANumber
        );
        $response->assertStatus(422);

        $dataWithInvalidCpf = ['cpf' => '99A.999.999/60'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidCpf
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_password_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidPasswordAsANumber = ['password' => 123, 'new_password' => 'any_password'];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidPasswordAsANumber
        );
        $response->assertStatus(422);

        $dataWithInvalidNewPasswordAsANumber = ['password' => 'any_password', 'new_password' => 123];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidNewPasswordAsANumber
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_invalid_phones_are_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());
        $authenticatedRoute = $this->actingAs($user, 'api');

        $dataWithInvalidPhoneNumbers = [
            'phones' => [
                [ 'number' => '9999A99999' ],
                [ 'number' => '(84) 99999999' ]
            ]
        ];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithInvalidPhoneNumbers
        );
        $response->assertStatus(422);

        $dataWithSamePhoneNumbers = [
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(99) 99999-9999' ]
            ]
        ];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $dataWithSamePhoneNumbers
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_an_existing_cpf_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());
        $authenticatedRoute = $this->actingAs($user, 'api');

        factory(User::class)->create(['cpf' => '111.111.111-11', 'user_type' => 'ADMIN_COOP']);
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11', // same cpf
            'phones' => [
                [ 'number' => '(99) 99999-9999' ],
                [ 'number' => '(88) 88888-8888' ]
            ]
        ];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $data
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function should_throw_an_error_if_an_existing_phone_is_passed_on_update()
    {
        $user = factory(User::class)->create(['user_type' => 'ADMIN_CONAB']);
        $cooperative = factory(Cooperative::class)->create();
        $admin = factory(User::class)->make([
            'user_type' => 'ADMIN_COOP',
            'password' => '123456'
        ]);
        $cooperative->admins()->save($admin);
        $admin->phones()->save(factory(\App\Phone::class)->make());

        $authenticatedRoute = $this->actingAs($user, 'api');
        $phone = factory(Phone::class)->create();
        $data = [
            'name' => 'any_name',
            'email' => 'any@email.com',
            'cpf' => '111.111.111-11',
            'phones' => [
                [ 'number' => $phone->number ],
            ]
        ];
        $response = $authenticatedRoute->putJson(
            "/api/cooperatives/$cooperative->id/admins/$admin->id",
            $data
        );
        $response->assertStatus(422);
    }
}
