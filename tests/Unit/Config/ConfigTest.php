<?php

namespace Macmotp\Codegen\Tests\Unit\Config;

use Macmotp\Codegen\Config\Config;
use Macmotp\Codegen\Exceptions\InvalidCodegenConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config();
    }

    public function testConfigSetsCodeLength()
    {
        $this->config->setCodeLength(10);
        $this->assertEquals(10, $this->config->getCodeLength());
    }

    public function testConfigSetsMaxAttempts()
    {
        $this->config->setMaxAttempts(10);
        $this->assertEquals(10, $this->config->getMaxAttempts());
    }

    public function testExceptionOnConfigSetsMaxAttempts()
    {
        $this->expectException(InvalidCodegenConfigurationException::class);
        $this->expectExceptionMessage('The maximum allowed number of attempts is 10000');
        $this->config->setMaxAttempts(20000);
    }

    public function testConfigSetsPrependString()
    {
        $this->config->prepend('ST');
        $this->assertEquals('ST', $this->config->getPrependString());
    }

    public function testConfigSetsAppendString()
    {
        $this->config->append('ST');
        $this->assertEquals('ST', $this->config->getAppendString());
    }

    public function testConfigSetsSanitizeLevel()
    {
        $this->config->setSanitizeLevel(Config::SANITIZE_LEVEL_HIGH);
        $this->assertEquals(Config::SANITIZE_LEVEL_HIGH, $this->config->getSanitizeLevel());
        $this->assertEquals(Config::SANITIZE_REGEX[Config::SANITIZE_LEVEL_HIGH], $this->config->getSanitizeRegex());
    }

    public function testExceptionOnConfigSetsSanitizeLevel()
    {
        $this->expectException(InvalidCodegenConfigurationException::class);
        $this->expectExceptionMessage('Sanitize level not found');
        $this->config->setSanitizeLevel(5);
    }

    /**
     * @dataProvider listInvalidCodeLengthExceptionCases
     *
     * @param int $codeLength
     * @param string $prependString
     * @param string $appendString
     *
     * @throws InvalidCodegenConfigurationException
     */
    public function testExceptionOnConfigSetsCodeLength(int $codeLength, string $prependString, string $appendString)
    {
        $this->expectException(InvalidCodegenConfigurationException::class);
        $this->expectExceptionMessage('The length of the code must be more than 3 characters, check the length of the code length, prepend and append string');
        $this->config->setCodeLength($codeLength)->prepend($prependString)->append($appendString);
    }

    /**
     * @dataProvider listInvalidCodeLengthExceptionCases
     *
     * @param int $codeLength
     * @param string $prependString
     * @param string $appendString
     *
     * @throws InvalidCodegenConfigurationException
     */
    public function testExceptionOnConfigSetsCodeLengthInverseOrder(int $codeLength, string $prependString, string $appendString)
    {
        $this->expectException(InvalidCodegenConfigurationException::class);
        $this->expectExceptionMessage('The length of the code must be more than 3 characters, check the length of the code length, prepend and append string');
        $this->config->append($appendString)->setCodeLength($codeLength)->prepend($prependString);
    }

    /**
     * List of InvalidCodeException cases
     *
     * @return array[]
     */
    public function listInvalidCodeLengthExceptionCases(): array
    {
        return [
            [2, '', ''],
            [5, '12', '3'],
            [7, '123', '45'],
        ];
    }
}
