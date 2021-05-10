<?php

namespace Macmotp\Codegen\Tests\Unit\Config;

use Macmotp\Codegen\Config\Config;
use Macmotp\Codegen\Config\Sanitizer;
use Macmotp\Codegen\Exceptions\InvalidCodegenConfigurationException;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    private Sanitizer $sanitizer;
    private Config $config;

    protected function setUp(): void
    {
        $this->sanitizer = new Sanitizer();
        $this->config = new Config();
    }

    /**
     * @dataProvider listSanitizeCases
     *
     * @param string $source
     * @param int $sanitizeLevel
     * @param array $result
     *
     * @throws InvalidCodegenConfigurationException
     */
    public function testSanitizerFiltersUnwantedLetters(string $source, int $sanitizeLevel, array $result)
    {
        $sanitizeRegex = $this->config->setSanitizeLevel($sanitizeLevel)->getSanitizeRegex();

        $this->assertEquals($result, $this->sanitizer->setSanitizeRegex($sanitizeRegex)->sanitize($source));
    }

    /**
     * List of sanitize cases
     *
     * @return array[]
     */
    public function listSanitizeCases(): array
    {
        return [
            // Try different sanitize level on latin character
            ['Sirio the ecommerce for the future', Config::SANITIZE_LEVEL_LOW, ['SIRIO', 'ECOMMERCE', 'FUTURE']],
            ['Sirio the ecommerce for the future', Config::SANITIZE_LEVEL_MEDIUM, ['SR', 'ECMMERCE', 'FUTURE']],
            ['Sirio the ecommerce for the future', Config::SANITIZE_LEVEL_HIGH, ['R', 'ECMMERCE', 'FTRE']],
            // Try trim spaces
            ['  JOHN  ', Config::SANITIZE_LEVEL_MEDIUM, ['JHN']],
            ['  JOHN  DOE ', Config::SANITIZE_LEVEL_MEDIUM, ['JHN', 'DE']],
            // Try different sanitize level on non latin character
            ['Company Name: a lot/ of ! non $ accepted. Char_act&rs', Config::SANITIZE_LEVEL_LOW, ['COMPANY', 'NAME', 'A', 'LOT', 'NON', 'ACCEPTED', 'CHARACTRS']],
            ['Jöhn Doë', Config::SANITIZE_LEVEL_LOW, ['JOHN', 'DOE']],
            ['Jöhn Doë', Config::SANITIZE_LEVEL_MEDIUM, ['JHN', 'DE']],
            ['ありがとうございました', Config::SANITIZE_LEVEL_MEDIUM, ['ARGATUGZAMASHTA']],
            ['ありがとうございました', Config::SANITIZE_LEVEL_HIGH, ['RGTGMHT']],
            ['cảm ơn bạn', Config::SANITIZE_LEVEL_MEDIUM, ['CAM', 'N', 'BAN']],
            ['Привет русский народ', Config::SANITIZE_LEVEL_HIGH, ['PRET', 'RKJ', 'NRD']],
        ];
    }
}
