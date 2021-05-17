<?php

namespace Macmotp\Codegen\Tests\Unit\Generator;

use Macmotp\Codegen\Generator\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class GeneratorTest
 *
 * @package Macmotp\Codegen\Tests\Unit\Generator
 * @group Generator
 */
class GeneratorTest extends TestCase
{
    private Generator $generator;

    protected function setUp(): void
    {
        $this->generator = new Generator();
    }

    /**
     * @dataProvider listDifferentSources
     *
     * @param string $source
     * @param string $code
     */
    public function testGeneratorGeneratesCodes(string $source, string $code)
    {
        $result = $this->generator->generate($source);

        $this->assertEquals($code, $result);
        $this->assertIsString($result);
    }

    public function testGeneratorAppendsRandomCharacters()
    {
        $code = $this->generator->generate('ABC');

        $this->assertEquals(6, strlen($code));
        $this->assertEquals('ABC', substr($code, 0, 3));
    }

    /**
     * List of different sources
     *
     * @return array[]
     */
    public function listDifferentSources(): array
    {
        return [
            ['JOHN DOE', 'JOHNDO'],
            ['COMPANY NAME', 'COMPNA'],
            ['SIRIO ECOMMERCE FUTURE', 'SECOFU'],
        ];
    }
}
