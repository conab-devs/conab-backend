<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function should_return_validation_error_if_no_name_is_passed()
    {
        $response = $this->postJson('api/users', [
            'email' => 'valid_mail@mail.com',
            'password' => 'valid_password',
            'cpf' => 'valid_cpf',
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
        ]);
        $response->assertStatus(422);
    }
}
