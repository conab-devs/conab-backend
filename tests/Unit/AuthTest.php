<?php

namespace Tests\Unit;

use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery;
use App\Exceptions\ServerError;
use App\Exceptions\UnauthorizedException;
use App\Components\Auth\AuthHandler;
use App\Components\Auth\TokenGenerator\JwtGenerator;

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
        $tokenGenerator->shouldReceive('generate')->never()->andReturn($token);

        return new AuthHandler($tokenGenerator);
    }

    /** @test */
    public function should_throw_error_if_email_not_passed()
    {
        $this->expectException(ServerError::class);
        $this->sut->authenticate([
            'password' => 'valid_password',
        ]);
    }

    /** @test */
    public function should_throw_error_if_password_not_passed()
    {
        $this->expectException(ServerError::class);
        $this->sut->authenticate([
            'email' => 'valid_mail@mail.com',
        ]);
    }

    public function should_throw_error_if_login_fails()
    {
        $this->expectException(UnauthorizedException::class);

        $tokenGenerator = Mockery::mock(JwtGenerator::class);
        $tokenGenerator->shouldReceive('generate')
            ->once()
            ->with([
                'email' => 'valid@valid.com', 
                'password' => 'invalid_password'
            ])
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
            ->with([
                'email' => 'valid@valid.com', 
                'password' => 'valid_password'
            ])
            ->once()
            ->andReturn('valid_token');

        $sut = new AuthHandler($tokenGenerator);

        $response = $sut->authenticate([
            'email' => 'valid@valid.com', 
            'password' => 'valid_password'
        ]);

        $this->assertEquals('valid_token', $response['token']);
    }
}
