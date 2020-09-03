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
}
