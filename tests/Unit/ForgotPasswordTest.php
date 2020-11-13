<?php

namespace Tests\Unit;

use App\Components\Auth\ForgotPasswordHandler;
use App\Components\Auth\TokenGenerator\CodeGenerator;
use App\Exceptions\ServerError;
use App\Exceptions\UnauthorizedException;
use App\PasswordReset;
use App\User;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

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
        $this->generator = Mockery::mock(CodeGenerator::class);
    }


    /** @test */
    public function should_throw_error_if_email_not_passed()
    {
        $this->expectException(ServerError::class);
        $sut = new ForgotPasswordHandler($this->passwordReset,
                                        $this->generator,
                                        $this->user);

        $sut->resetPassword([
            'code' => 123456,
        ]);
    }

    /** @test */
    public function should_throw_error_if_code_not_passed()
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
    public function string_token_generator_should_return_code()
    {
        $sut = new CodeGenerator();
        $code = $sut->generate();
        $this->assertNotEmpty($code);
    }

    /** @test */
    public function generate_token_should_return_password_reset_code_if_it_already_exists()
    {
        $queriedReset = Mockery::mock(PasswordReset::class);
        $queriedReset->shouldReceive('getAttribute')
            ->with('code')
            ->andReturn(123456);
        $queriedReset->shouldReceive('getAttribute')
            ->with('email')
            ->twice()
            ->andReturn('valid_mail@mail.com');

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $queriedReset->email)
            ->once()
            ->andReturn($queriedReset);

        $this->generator->shouldReceive('generate')
            ->never()
            ->andReturn(123456);

        $sut = new ForgotPasswordHandler($this->passwordReset,
                                        $this->generator,
                                        $this->user);

        $code = $sut->generateToken($queriedReset->email);

        $this->assertEquals($queriedReset->code, $code);
    }


    /** @test */
    public function generate_token_should_return_code()
    {
        $code = 123456;
        $email = 'valid_mail@mail.com';

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $email)
            ->once()
            ->andReturn(null);
        $this->passwordReset->shouldReceive('fill')
            ->with(['email' => $email, 'code' => $code])
            ->once()
            ->andReturn(null);
        $this->passwordReset->shouldReceive('save')
            ->once()
            ->andReturn(1);

        $this->generator->shouldReceive('generate')->once()->andReturn($code);

        $sut = new ForgotPasswordHandler($this->passwordReset,
                                        $this->generator,
                                        $this->user);

        $this->assertEquals($code, $sut->generateToken($email));
    }


    /** @test */
    public function ensure_that_reset_request_is_sent_with_valid_code_and_mail()
    {
        $email = 'valid_mail@mail.com';
        $code = 123456;

        Mail::fake();

        $this->passwordReset->shouldReceive('firstWhere')
            ->with('email', $email)
            ->once()
            ->andReturn(null);
        $this->passwordReset->shouldReceive('fill')
            ->with(['email' => $email, 'code' => $code])
            ->once()
            ->andReturn(null);
        $this->passwordReset->shouldReceive('save')
            ->once()
            ->andReturn(1);

        $this->generator->shouldReceive('generate')->once()->andReturn($code);

        $sut = new ForgotPasswordHandler($this->passwordReset,
                                        $this->generator,
                                        $this->user);

        $sut->sendResetRequest($email);

        Mail::assertSent(function (\App\Mail\ResetMail $mail) use ($code) {
            return $mail->code === $code;
        });
    }

    /** @test */
    public function reset_password_should_return_unauthorized()
    {
        $this->expectException(UnauthorizedException::class);

        $this->passwordReset->shouldReceive('where->count')->once()->andReturn(0);

        (new ForgotPasswordHandler($this->passwordReset,
                                  $this->generator,
                                  $this->user))
            ->resetPassword([
                'email' => 'invalid_email',
                'password' => 'new_password',
                'code' => 123456,
            ]);
    }

    /** @test */
    public function reset_password_should_update_user_password()
    {
        $email = 'valid_mail@mail.com';
        $newPassword = 'new_password';

        $queriedUser = Mockery::mock(User::class);
        $queriedUser->shouldReceive('update')
            ->once()
            ->with(['password' => $newPassword]);

        $this->user->shouldReceive('firstWhere')
            ->with('email', $email)
            ->once()
            ->andReturn($queriedUser);

        $queriedReset = Mockery::mock(PasswordReset::class);
        $queriedReset->shouldReceive('delete')->once();

        $queriedReset->shouldReceive('getAttribute')
        ->with('created_at')
        ->andReturn(now());

        $this->passwordReset->shouldReceive('where->count')
            ->once()
            ->andReturn(1);
        $this->passwordReset->shouldReceive('where->first')
            ->once()
            ->andReturn($queriedReset);

        (new ForgotPasswordHandler($this->passwordReset,
                                  $this->generator,
                                  $this->user))
            ->resetPassword([
                'email' => $email,
                'password' => $newPassword,
                'code' => 123456,
            ]);
    }
}
