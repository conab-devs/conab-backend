<?php

namespace Tests\Unit;

use App\Components\Repositorys\PasswordResetRepository;
use App\Components\TokenGenerator;
use App\PasswordReset;
use PHPUnit\Framework\TestCase;
use \Mockery;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/** @group Franklyn */
class PasswordResetServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function makePasswordResetRepository($model = null, $tokenGenerator = null)
    {
        $model = $model ? $model : new PasswordReset();
        $tokenGenerator = $tokenGenerator ? $tokenGenerator : new TokenGenerator;
        return new PasswordResetRepository($model, $tokenGenerator);
    }

    /** @test */
    public function password_reset_repository_query_by_email_should_return_query_builder()
    {
        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->count')->andReturn(1);

        $service = $this->makePasswordResetRepository($model);
        $queryBuilder = $service->queryByEmail('valid_email');

        $this->assertGreaterThan(0, $queryBuilder->count());
    }

    /** @test */
    public function password_reset_repository_find_by_email_should_return_null()
    {
        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->first')->andReturn(null);

        $service = $this->makePasswordResetRepository($model);
        $passwordReset = $service->findByEmail('invalid_email');

        $this->assertEquals(null, $passwordReset);
    }

    /** @test */
    public function password_reset_repository_find_by_email_should_return_password_reset()
    {
        $reset = new PasswordReset();
        $reset->token = 'valid_token';
        $reset->email = 'valid_mail@mail.com';

        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->first')->andReturn($reset);

        $service = $this->makePasswordResetRepository($model);
        $passwordReset = $service->findByEmail($reset->email);

        $this->assertEquals($reset->email, $passwordReset->email);
        $this->assertEquals($reset->token, $passwordReset->token);
    }

    /** @test */
    public function password_reset_repository_should_store_reset_request()
    {
        $fields = ['email' => 'valid_mail@mail.com', 'token' => 'valid_token'];

        $tokenGenerator = Mockery::mock(TokenGenerator::class);
        $tokenGenerator->shouldReceive('generate')->andReturn($fields['token']);

        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('fill')->with($fields);
        $model->shouldReceive('save')->andReturn(true);

        $sut = $this->makePasswordResetRepository($model, $tokenGenerator);

        $this->assertTrue($sut->storePasswordResetRequest($fields['email'], $fields['token']));
    }
}
