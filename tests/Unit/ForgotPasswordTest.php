<?php

namespace Tests\Unit;

use App\Components\Errors\UnauthorizedException;
use App\Components\ForgotPasswordHandler;
use App\Components\TokenGenerator\StringGenerator;
use App\PasswordReset;
use App\User;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;
use App\Components\Errors\ServerError;

/** @author Franklyn */
class ForgotPasswordTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $passwordReset;
    private $user;
    private $generator;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordReset = Mockery::mock(PasswordReset::class);
        $this->user = Mockery::mock(User::class);
        $this->generator = Mockery::mock(StringGenerator::class);
    }

    /** @test */
    public function should_throw_error_if_email_not_passed()
    {
        $this->expectException(ServerError::class);
        $sut = new ForgotPasswordHandler($this->passwordReset, 
                                        $this->generator, 
                                        $this->user);

        $sut->resetPassword([
            'token'> 'valid_token'
        ]);
    }

    /** @test */
    public function should_throw_error_if_token_not_passed()
    {
        $this->expectException(ServerError::class);
        $sut = new ForgotPasswordHandler($this->passwordReset, 
                                        $this->generator, 
                                        $this->user);
        $sut->resetPassword([
            'email' => 'valid_mail@mail.com',
        ]);
    }

    /** @test */
    public function string_token_generator_should_return_token()
    {
        $sut = new StringGenerator();
        $token = $sut->generate();
        $this->assertNotEmpty($token);
    }

    /** @test */
    public function generate_token_should_return_password_reset_token_if_it_already_exists()
    {
        $spy = Mockery::mock(PasswordReset::class);
        $spy->shouldReceive('getAttribute')
            ->with('token')
            ->andReturn('existent_token');
        $spy->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn('valid_mail@mail.com');

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $spy->email)
            ->andReturn($spy);

        $this->generator->shouldReceive('generate')->andReturn('valid_token');

        $sut = new ForgotPasswordHandler($this->passwordReset, 
                                        $this->generator, 
                                        $this->user);

        $token = $sut->generateToken($spy->email);

        $this->assertEquals($spy->token, $token);
    }

    /** @test */
    public function generate_token_should_return_token()
    {
        $token = 'valid_token';
        $email = 'valid_mail@mail.com';

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $email)
            ->andReturn(null);
        $this->passwordReset->shouldReceive('fill')
            ->with(['email' => $email, 'token' => $token])
            ->andReturn(null);
        $this->passwordReset->shouldReceive('save')
            ->andReturn(1);

        $this->generator = Mockery::mock(StringGenerator::class);
        $this->generator->shouldReceive('generate')->andReturn($token);

        $sut = new ForgotPasswordHandler($this->passwordReset, 
                                        $this->generator, 
                                        $this->user);

        $this->assertEquals('valid_token', $sut->generateToken($email));
    }

    /** @test */
    public function ensure_that_reset_request_is_sent_with_valid_token_and_mail()
    {
        $email = 'valid_mail@mail.com';
        $token = 'valid_token';

        Mail::fake();

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $email)
            ->andReturn(null);
        $this->passwordReset->shouldReceive('fill')
            ->with(['email' => $email, 'token' => $token])
            ->andReturn(null);
        $this->passwordReset->shouldReceive('save')
            ->andReturn(1);

        $this->generator->shouldReceive('generate')->andReturn($token);

        $sut = new ForgotPasswordHandler($this->passwordReset, 
                                        $this->generator, 
                                        $this->user);

        $sut->sendResetRequest($email);

        Mail::assertSent(function (\App\Mail\ResetMail $mail) use ($token, $email) {
            return $mail->token === $token && $mail->email === $email;
        });
    }

    /** @test */
    public function reset_password_should_return_unauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $this->passwordReset->shouldReceive('where->count')->andReturn(0);

        (new ForgotPasswordHandler($this->passwordReset, 
                                  $this->generator, 
                                  $this->user))
            ->resetPassword([
                'email' => 'invalid_email',
                'password' => 'new_password',
                'token' => 'valid_token',
            ]);
    }

    /** @test */
    public function reset_password_should_update_user_password()
    {
        $email = 'valid_mail@mail.com';
        $newPassword = 'new_password';

        $userSpy = Mockery::mock(User::class);
        $userSpy->shouldReceive('update')->with(['password' => $newPassword]);

        $this->user->shouldReceive('firstWhere')
            ->with('email', $email)
            ->andReturn($userSpy);

        $resetSpy = Mockery::mock(PasswordReset::class);
        $resetSpy->shouldReceive('delete')->once();

        $this->passwordReset->shouldReceive('where->count')->andReturn(1);
        $this->passwordReset->shouldReceive('where->first')->andReturn($resetSpy);

        (new ForgotPasswordHandler($this->passwordReset, 
                                  $this->generator, 
                                  $this->user))
            ->resetPassword([
                'email' => $email,
                'password' => $newPassword,
                'token' => 'valid_token',
            ]);
    }
}
