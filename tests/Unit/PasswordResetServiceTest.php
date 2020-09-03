<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \Mockery;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use App\PasswordReset;
use App\Components\Services\PasswordResetService;
use App\User;

/** @group Franklyn */
class PasswordResetServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function password_reset_service_query_by_email_should_return_query_builder()
    {
        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->count')->andReturn(1);
        
        $service = new PasswordResetService($model);
        $queryBuilder = $service->queryByEmail('valid_email');
        
        $this->assertGreaterThan(0, $queryBuilder->count());
    }

    /** @test */
    public function password_reset_service_find_by_email_should_return_null()
    {
        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->first')->andReturn(null);
        
        $service = new PasswordResetService($model);
        $passwordReset = $service->findByEmail('invalid_email');
        
        $this->assertEquals(null, $passwordReset);
    }

    /** @test */
    public function password_reset_service_find_by_email_should_return_password_reset()
    {
        $reset = new PasswordReset();
        $reset->token = 'valid_token';
        $reset->email = 'valid_mail@mail.com';

        $model = Mockery::mock(PasswordReset::class);
        $model->shouldReceive('where->first')->andReturn($reset);
        
        $service = new PasswordResetService($model);
        $passwordReset = $service->findByEmail($reset->email);
        
        $this->assertEquals($reset->email, $passwordReset->email);
        $this->assertEquals($reset->token, $passwordReset->token);

    }
}
