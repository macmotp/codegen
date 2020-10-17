<?php

namespace Macmotp\Codegen\Tests\Unit;

use Macmotp\Codegen;
use PHPUnit\Framework\TestCase;

class CodegenTest extends TestCase
{
    private Codegen $generator;

    protected function setUp(): void
    {
        $this->generator = new Codegen();
    }

    public function testCodegenMakesSemanticCodes()
    {
        $this->assertIsString($this->generator->make('string')->forHumans());
    }
}
