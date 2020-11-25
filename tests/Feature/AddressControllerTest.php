<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_addresses_is_not_passed()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', []);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_addresses()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_street_not_passed_to_address()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => [
                [],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_street_is_not_a_string()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => [
                ['street' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_neighborhood_not_passed_to_address()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => [
                ['street' => 'valid_street'],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_neighborhood_is_not_a_string()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
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
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
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
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
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
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
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
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => [
                [
                    'street' => 'valid_street',
                    'neighborhood' => 'valid_neighborhood',
                    'city' => 'valid_city',
                    'number' => 123456,
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_store_an_address()
    {
        $user = factory(\App\User::class)->create();

        $address_specification = [
            'street' => 'valid_street',
            'neighborhood' => 'valid_neighborhood',
            'city' => 'valid_city',
            'number' => 'valid_num',
        ];

        $response = $this->actingAs($user)->postJson('api/addresses', [
            'addresses' => [$address_specification],
        ]);
        $response->assertCreated();
        $response->assertJsonFragment($address_specification);

        $this->assertDatabaseHas('addresses', [
            'street' => 'valid_street',
        ]);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_addresses_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/addresses", [
            'addresses' => 123,
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_street_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/addresses", [
            'addresses' => [
                ['street' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_neighborhood_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/addresses", [
            'addresses' => [
                ['neighborhood' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_city_is_not_a_string_on_updated()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/addresses", [
            'addresses' => [
                ['city' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function should_return_validation_error_if_number_is_not_a_string_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/addresses", [
            'addresses' => [
                ['number' => 123456],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_address()
    {
        $user = factory(\App\User::class)->create();

        $address_specification = [
            'street' => 'valid_street',
            'neighborhood' => 'valid_neighborhood',
            'city' => 'valid_city',
            'number' => 'number',
        ];

        $user->addresses()->create(
            factory(\App\Address::class)->make()->toArray()
        );

        $response = $this->actingAs($user)
            ->putJson("api/addresses", [
                'addresses' => $address_specification,
            ]);
        $response->assertStatus(200);

        $response->assertJsonFragment($address_specification);
    }

    /** @test */
    public function should_get_user_addresses()
    {
        $user = factory(\App\User::class)->create();

        $address = $user->addresses()
            ->create(factory(\App\Address::class)
                    ->make()
                    ->toArray());

        $response = $this->actingAs($user)->getJson('api/addresses');

        $response->assertJsonFragment($address->toArray());
    }
}
