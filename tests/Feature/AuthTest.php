<?php

namespace Tests\Feature;

use App\PasswordReset;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** @author feat */
class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private $credentials;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'email' => $this->faker()->unique()->safeEmail,
            'password' => 'valid_password',
            'user_type' => 'ADMIN_CONAB',
        ];

        $this->user = factory(User::class)->create($this->credentials);
    }

    /** @test */
    public function should_make_login_and_return_token()
    {
        $response = $this->postJson('/api/login', $this->credentials);
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user', $response);
        $this->assertGreaterThan(0, count($response['user']));
    }

    /** @test */
    public function should_make_login_and_access_get_route_with_success()
    {
        $token = auth()->attempt($this->credentials);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/conab/admins');

        $response->assertStatus(200);
    }

    /** @test */
    public function should_update_the_user_password_if_reset_token_is_valid()
    {
        $requestResponse = $this->postJson(
            '/api/password/reset/request',
            ['email' => $this->user->email]
        );
        $requestResponse->assertStatus(200);

        $code = (PasswordReset::where('email', $this->user->email)->first())->code;

        $resetResponse = $this->postJson(
            '/api/password/reset',
            [
                'email' => $this->user->email,
                'password' => 'new_password',
                'password_confirmation' => 'new_password',
                'code' => $code,
            ]
        );
        $resetResponse->assertStatus(200);

        $this->user->refresh();

        $this->assertTrue(Hash::check('new_password', $this->user->password));
    }

    /** @test */
    public function should_try_to_update_user_password_with_expired_code_and_return_error()
    {
        $passwordReset = new \App\PasswordReset();
        $passwordReset->fill([
            'email' => $this->user->email,
            'code' => 333333,
            'created_at' => now()->subDay(),
        ]);
        $passwordReset->save();

        $resetResponse = $this->postJson(
            '/api/password/reset',
            [
                'email' => $this->user->email,
                'password' => 'new_password',
                'password_confirmation' => 'new_password',
                'code' => $passwordReset->code,
            ]
        );
        $resetResponse->assertStatus(401);
        $resetResponse->assertJsonFragment([
            "message" => "O código informado é inválido."
        ]);

        $this->assertDeleted('password_resets', [
            'code' => $passwordReset->code,
        ]);
    }

    /** @test */
    public function should_try_to_make_login_and_throw_error_if_credentials_invalid()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid_mail@mail.com',
            'password' => 'invalid_password',
        ]);
        $response->assertStatus(401);
    }
}
