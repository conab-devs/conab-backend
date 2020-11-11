<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** @author User  */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * User's store controller action
     */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $response = $this->postJson('api/users', [
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_is_passed_on_name()
    {
        $response = $this->postJson('api/users', [
            'name' => 123456,
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_email_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_invalid_email_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'invalid_email',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_email_already_exists()
    {
        factory(\App\User::class)->create(['email' => 'valid_mail@mail.com']);

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_password_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_password_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 123456,
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_password_length_is_lesser_than_6()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '1234',
            'cpf' => 'valid_cpf',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_no_cpf_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_cpf_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => 1234,
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_cpf_with_wrong_format_is_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '1234',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_existing_cpf_is_passed()
    {
        factory(\App\User::class)->create(['cpf' => '123.123.123-12']);

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'any_num',
                ],
                [
                    'street' => 'another_street',
                    'neighborhood' => 'another_neighborhood',
                    'city' => 'another_city',
                    'number' => 'any_num',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_addresses_is_not_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_addresses()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_street_not_passed_to_address()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_street_is_not_a_string()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                ['street' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_neighborhood_not_passed_to_address()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                ['street' => 'valid_street'],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_neighborhood_is_not_a_string()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 123456,
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_city_is_not_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_city_is_not_a_string()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 123456,
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_number_is_not_passed()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_number_is_not_a_string()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                ],
                'number' => 123456,
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_store_an_user()
    {
        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '123.123.123-12',
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'valid_num',
                ],
            ],
        ]);
        $response->assertCreated();
        $response->assertJsonStructure([
            'name', 'email', 'cpf', 'id', 'updated_at', 'created_at',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'valid_mail@mail.com',
        ]);
    }

    /** @test */
    public function should_store_addresses_with_user()
    {
        $addresses = [
            [
                'street' => 'valid_street',
                'neighborhood' => 'valid_neighborhood',
                'city' => 'valid_city',
                'number' => 'valid_num',
            ],
            [
                'street' => 'another_street',
                'neighborhood' => 'another_neighborhood',
                'city' => 'another_city',
                'number' => 'another_num',
            ],
        ];

        $response = $this->postJson('api/users', [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => '123.123.123-12',
            'addresses' => $addresses,
        ]);
        $response->assertJsonStructure(['addresses']);

        for ($address = 0; $address < count($addresses); $address++) {
            $this->assertDatabaseHas('addresses', [
                'street' => $addresses[$address]['street'],
            ]);
        }
    }

    /** @test */
    public function should_return_validation_error_if_integer_is_passed_on_name_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'name' => 123456,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_invalid_email_is_passed_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'email' => 'invalid_email',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_email_already_exists_on_update()
    {
        $user = factory(\App\User::class)->create([
            'email' => 'valid_mail@mail.com',
        ]);

        $response = $this->actingAs($user)->putJson("api/users", [
            'email' => 'valid_mail@mail.com',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_password_is_passed_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'password' => 123456,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_password_length_is_lesser_than_6_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'password' => '1234',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_integer_cpf_is_passed_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'cpf' => 1234,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_cpf_with_wrong_format_is_passed_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'cpf' => '1234',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_existing_cpf_is_passed_on_update()
    {
        $user = factory(\App\User::class)->create(['cpf' => '123.123.123-12']);

        $response = $this->actingAs($user)->putJson("api/users", [
            'cpf' => '123.123.123-12',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_addresses_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'addresses' => 123,
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_street_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'addresses' => [
                ['street' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_neighborhood_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'addresses' => [
                ['neighborhood' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_city_is_not_a_string_on_updated()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'addresses' => [
                ['city' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_number_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/users", [
            'addresses' => [
                ['number' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_user()
    {
        $user = factory(\App\User::class)->create([
            'password' => '123456'
        ]);

        $new_informations = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => '123456',
            'new_password' => 'an_password',
            'cpf' => '123.123.123-12',
        ];

        $response = $this->actingAs($user)
            ->putJson("api/users", $new_informations);
        $response->assertStatus(200);

        $expected_response = array_diff_assoc($new_informations, [
            'password' => '123456',
            'new_password' => 'an_password',
        ]);

        $response->assertJson($expected_response);

        $user->refresh();

        $this->assertTrue(
            Hash::check($new_informations['new_password'], $user->password)
        );
    }

    /** @test */
    public function should_update_user_without_password()
    {
        $user = factory(\App\User::class)->create();

        $new_informations = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'cpf' => '123.123.123-12',
        ];

        $response = $this->actingAs($user)
            ->putJson("api/users", $new_informations);
        $response->assertStatus(200);

        $response->assertJson($new_informations);
    }

    /** @test */
    public function should_return_validation_error_if_repeated_password_is_sent()
    {
        $user = factory(\App\User::class)->create(['password' => 'valid_pass']);

        $older_password = 'valid_pass';

        $new_informations = [
            'name' => 'valid_name',
            'email' => 'valid_mail@mail.com',
            'password' => $older_password,
            'cpf' => '123.123.123-12',
        ];

        $response = $this->actingAs($user)
            ->putJson("api/users", $new_informations);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_user_address()
    {
        $user = factory(\App\User::class)->create();

        $user->addresses()
            ->create(factory(\App\Address::class)
            ->make()
            ->toArray());

        $new_address = [
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 'AB12',
                ],
            ],
        ];

        $response = $this->actingAs($user)
            ->putJson('api/users', $new_address);
        $response->assertStatus(200);

        $response->assertJson($new_address);
    }

   /** @test */
   public function should_get_the_authenticated_user_data()
   {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->getJson('api/users');
        $response->assertStatus(200);

        $response->assertJson($user->toArray());
   }

   /** @test */
   public function should_allow_customers_to_delete_their_accounts_own_accounts()
   {
        $user = factory(\App\User::class)->create(['user_type' => 'CUSTOMER']);

        $response = $this->actingAs($user)->deleteJson("api/users/$user->id");
        $response->assertStatus(204);

        $this->assertDeleted('users', $user->toArray());
   }
}
