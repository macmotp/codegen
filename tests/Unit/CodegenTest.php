<?php

namespace Macmotp\Codegen\Tests\Unit;

use Macmotp\Codegen;
use Macmotp\Codegen\Config\Config;
use PHPUnit\Framework\TestCase;

/**
 * Class CodegenTest
 *
 * @package Macmotp\Codegen\Tests\Unit
 * @group Codegen
 */
class CodegenTest extends TestCase
{
    private Codegen $codegen;

    protected function setUp(): void
    {
        $this->codegen = new Codegen();
    }

    public function testCodegenUsesConfiguration()
    {
        $code = $this->codegen
            ->setCodeLength(8)
            ->prepend('PR')
            ->append('AP')
            ->generate('Company Name');

        $this->assertEquals(8, strlen($code));
        $this->assertEquals('PR', substr($code, 0, 2));
        $this->assertEquals('AP', substr($code, -2));
    }

    public function testCodegenMaxAttemptsWithSingleCode()
    {
        $this->codegen->setMaxAttempts(3);
        for ($i = 0; $i < 4; $i++) {
            $this->codegen->generate('Company Name');
        }

        $this->assertCount(4, $this->codegen->getCollection());
    }

    public function testCodegenMaxAttemptsWithCollection()
    {
        $this->codegen
            ->setMaxAttempts(3)
            ->collection('Company Name', 4);

        $this->assertCount(3, $this->codegen->getCollection());
    }

    /**
     * @dataProvider listDifferentSanitizeLevels
     *
     * @param int $sanitizeLevel
     * @param string $source
     * @param string $regex
     */
    public function testCodegenSetsSanitizeLevel(int $sanitizeLevel, string $source, string $regex)
    {
        $code = $this->codegen
            ->setSanitizeLevel($sanitizeLevel)
            ->generate($source);

        $this->assertMatchesRegularExpression($regex, $code);
    }

    /**
     * List of different sanitize levels
     *
     * @return array[]
     */
    public function listDifferentSanitizeLevels(): array
    {
        return [
            'low' => [Config::SANITIZE_LEVEL_LOW, 'Bob Maclovin', '/[a-zA-Z0-9\s]/'],
            'medium' => [Config::SANITIZE_LEVEL_MEDIUM, 'Bob Maclovin', '/([IOQ]|[a-zA-Z2-9\s])/'],
            'high' => [Config::SANITIZE_LEVEL_HIGH, 'Bob Maclovin', '/([ABIOQSUVY458]|[c-wC-W3-9\s])/'],
        ];
    }
}
