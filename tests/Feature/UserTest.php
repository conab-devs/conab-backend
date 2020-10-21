<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $response = $this->postJson('api/users', [
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
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
            'phones' => [
                ['number' => '(00) 00000-0000'],
                ['number' => '(11) 11111-1111'],
            ],
            'addresses' => [
                ['street' => 'valid_street',
                 'neighborhood' => 'valid_neighborhood',
                 'city' => 'valid_city',
                 'number' => 'any_num'],
                ['street' => 'another_street',
                 'neighborhood' => 'another_neighborhood',
                 'city' => 'another_city',
                 'number' => 'any_num'],
            ],
        ]);
        $response->assertStatus(422);
    }
}
