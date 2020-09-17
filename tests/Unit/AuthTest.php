<?php

namespace Tests\Unit;

use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery;
use App\Components\Errors\ServerError;
use App\Components\Errors\UnauthorizedException;
use App\Components\AuthHandler;
use App\Components\TokenGenerator\JwtGenerator;

/** @author Franklyn */
class AuthTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->makeAuthHandler();
    }

    public function makeAuthHandler($token = 'valid_jwt_token')
    {
        $tokenGenerator = Mockery::mock(JwtGenerator::class);
        $tokenGenerator->shouldReceive('generate')->andReturn($token);

        return new AuthHandler($tokenGenerator);
    }

    /** @test */
    public function should_throw_error_if_email_not_passed()
    {
        $this->expectException(ServerError::class);
        $this->sut->authenticate([
            'password' => 'valid_password',
            'device_name'> 'valid_device_name'
        ]);
    }

    /** @test */
    public function should_throw_error_if_password_not_passed()
    {
        $this->expectException(ServerError::class);
        $this->sut->authenticate([
            'email' => 'valid_mail@mail.com',
            'device_name'> 'valid_device_name'
        ]);
    }

    public function should_throw_error_if_login_fails()
    {
        $this->expectException(UnauthorizedException::class);

        $tokenGenerator = Mockery::mock(JwtGenerator::class);
        $tokenGenerator->shouldReceive('generate')
            ->with(['email' => 'valid@valid.com', 'password' => 'invalid_password'])
            ->andReturn('');

        (new AuthHandler($tokenGenerator))->authenticate([
            'email' => 'valid@valid.com', 
            'password' => 'invalid_password'
        ]);
    }

    /** @test */
    public function should_return_token_if_authentication_succeed()
    {
        $tokenGenerator = Mockery::mock(JwtGenerator::class);
        $tokenGenerator->shouldReceive('generate')
            ->with(['email' => 'valid@valid.com', 'password' => 'valid_password'])
            ->andReturn('valid_token');

        $sut = new AuthHandler($tokenGenerator);

        $response = $sut->authenticate(['email' => 'valid@valid.com', 'password' => 'valid_password']);

        $this->assertEquals('valid_token', $response['token']);
    }
}
