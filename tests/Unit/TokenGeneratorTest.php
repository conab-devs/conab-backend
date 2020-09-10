<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Components\TokenGenerator\StringGenerator;

/** @author token */
class TokenGeneratorTest extends TestCase
{
    /** @test */
    public function string_token_generator_should_return_token()
    {
        $sut = new StringGenerator();

        $token = $sut->generate();

        $this->assertNotEmpty($token);
    }
}
