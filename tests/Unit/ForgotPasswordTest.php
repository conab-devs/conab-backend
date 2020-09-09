<?php

namespace Tests\Unit;

use App\Components\Errors\ServerError;
use App\Components\Errors\UnauthorizedException;
use App\Components\ForgotPasswordHandler;
use App\Components\Services\PasswordResetService;
use App\Components\Services\UserService;
use App\PasswordReset;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

/** @author Franklyn */
class ForgotPasswordTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function reset_request_should_return_password_reset_token_if_it_already_exists()
    {
        $passwordReset = Mockery::mock(PasswordReset::class);
        $passwordReset->shouldReceive('getAttribute')
            ->with('token')
            ->andReturn('existent_token');
        $passwordReset->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn('existent_mail@mail.com');

        $service = Mockery::mock(PasswordResetService::class);
        $service->shouldReceive('findByEmail')
            ->with($passwordReset->email)
            ->andReturn($passwordReset);

        $generator = Mockery::mock(TokenGenerator::class);
        $generator->shouldReceive('generate')->andReturn('valid_token');

        $userService = Mockery::mock(UserService::class);

        $sut = new ForgotPasswordHandler($service, $generator, $userService);

        $token = $sut->generateToken($passwordReset->email);

        $this->assertEquals($passwordReset->token, $token);
    }

    /** @test */
    public function reset_request_should_save_new_password_reset()
    {
        $this->expectException(ServerError::class);

        $service = Mockery::mock(PasswordResetService::class);
        $service->shouldReceive('findByEmail')
            ->with('existent_mail@mail.com')
            ->andReturn(null);

        $service->shouldReceive('storePasswordResetRequest')
            ->withArgs(['existent_mail@mail.com', 'valid_token'])
            ->andReturn(false);

        $generator = Mockery::mock(TokenGenerator::class);
        $generator->shouldReceive('generate')->andReturn('valid_token');

        $userService = Mockery::mock(UserService::class);

        $sut = new ForgotPasswordHandler($service, $generator, $userService);

        $sut->generateToken('existent_mail@mail.com');
    }

    /** @test */
    public function reset_request_should_return_token()
    {
        $service = Mockery::mock(PasswordResetService::class);
        $service->shouldReceive('findByEmail')
            ->with('existent_mail@mail.com')
            ->andReturn(null);

        $service->shouldReceive('storePasswordResetRequest')
            ->withArgs(['existent_mail@mail.com', 'valid_token'])
            ->andReturn(true);

        $generator = Mockery::mock(TokenGenerator::class);
        $generator->shouldReceive('generate')->andReturn('valid_token');

        $userService = Mockery::mock(UserService::class);

        $sut = new ForgotPasswordHandler($service, $generator, $userService);

        $token = $sut->generateToken('existent_mail@mail.com');

        $this->assertEquals('valid_token', $token);
    }

    /** @test */
    public function reset_password_should_return_unauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $userService = Mockery::mock(UserService::class);

        $passwordService = Mockery::mock(PasswordResetService::class);
        $passwordService->shouldReceive('find->count')->andReturn(0);

        $generator = Mockery::mock(TokenGenerator::class);

        (new ForgotPasswordHandler($passwordService, $generator, $userService))
            ->resetPassword([
                'email' => 'invalid_email', 
                'password' => 'new_password', 
                'token' => 'valid_token'
            ]);
    }
}
