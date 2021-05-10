<?php

namespace Macmotp\Codegen\Tests\Unit;

use Macmotp\Codegen;
use Macmotp\Codegen\Config\Config;
use PHPUnit\Framework\TestCase;

class CodegenTest extends TestCase
{
    private Codegen $generator;

    protected function setUp(): void
    {
        $this->generator = new Codegen();
    }

    /**
     * @dataProvider listDifferentSources
     *
     * @param string $source
     * @param string $code
     */
    public function testCodegenGeneration(string $source, string $code)
    {
        $result = $this->generator->generate($source);

        $this->assertEquals($code, $result);
        $this->assertIsString($result);
        $this->assertTrue(strlen($result) === 6);
    }

    public function testCodegenUsesConfiguration()
    {
        $config = new Config();
        $config->setCodeLength(8);
        $config->prepend('PR');
        $config->append('AP');

        $code = $this->generator->withConfig($config)->generate('Company Name');

        $this->assertEquals(8, strlen($code));
        $this->assertEquals('PR', substr($code, 0, 2));
        $this->assertEquals('AP', substr($code, -2));
    }

    /**
     * List of different sources
     *
     * @return array[]
     */
    public function listDifferentSources(): array
    {
        return [
            ['John Doe', 'JOHNDO'],
            ['Company Name', 'COMPNA'],
            ['Sirio the ecommerce for the future', 'SECOFU'],
        ];
    }
}
