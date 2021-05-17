<?php

namespace Macmotp;

use Macmotp\Codegen\Config\Config;
use Macmotp\Codegen\Config\Sanitizer;
use Macmotp\Codegen\Exceptions\InvalidCodegenConfigurationException;
use Macmotp\Codegen\Generator\Generator;

/**
 * Class Codegen
 *
 * @package Macmotp/Codegen
 */
class Codegen
{
    /**
     * The generator
     *
     * @var Generator
     */
    private Generator $generator;

    /**
     * The config object
     *
     * @var Config
     */
    private Config $config;

    /**
     * The sanitizer
     *
     * @var Sanitizer
     */
    private Sanitizer $sanitizer;

    /**
     * Collection of codes
     *
     * @var array
     */
    private array $collection;

    /**
     * Codegen constructor.
     */
    public function __construct()
    {
        $this->generator = new Generator();
        $this->config = new Config();
        $this->sanitizer = new Sanitizer();
        $this->resetCollection();
    }

    /**
     * Get the collection
     *
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * Set the code length
     *
     * @param int $codeLength
     *
     * @return self
     * @throws InvalidCodegenConfigurationException
     */
    public function setCodeLength(int $codeLength): self
    {
        $this->config->setCodeLength($codeLength);

        return $this;
    }

    /**
     * Set the max number of attempts
     *
     * @param int $maxAttempts
     *
     * @return self
     * @throws InvalidCodegenConfigurationException
     */
    public function setMaxAttempts(int $maxAttempts): self
    {
        $this->config->setMaxAttempts($maxAttempts);

        return $this;
    }

    /**
     * Set the prepend string
     *
     * @param string $prependString
     *
     * @return $this
     * @throws InvalidCodegenConfigurationException
     */
    public function prepend(string $prependString): self
    {
        $this->config->prepend($prependString);

        return $this;
    }

    /**
     * Set the append string
     *
     * @param string $appendString
     *
     * @return $this
     * @throws InvalidCodegenConfigurationException
     */
    public function append(string $appendString): self
    {
        $this->config->append($appendString);

        return $this;
    }

    /**
     * Set the sanitize level
     *
     * @param int $sanitizeLevel
     *
     * @return $this
     * @throws InvalidCodegenConfigurationException
     */
    public function setSanitizeLevel(int $sanitizeLevel): self
    {
        $this->config->setSanitizeLevel($sanitizeLevel);

        return $this;
    }

    /**
     * Generate a collection of codes
     *
     * @param string|null $source
     * @param int $count
     *
     * @return array
     */
    public function collection(?string $source, int $count = 1): array
    {
        $source ??= '';

        // Sanitize the source
        $source = $this->sanitizer->setSanitizeRegex($this->config->getSanitizeRegex())->sanitize($source);

        // Generate the codes
        for ($i = 0; $i < $this->checkAttempts($count); $i++) {
            $this->collection[] = $this->generator->withConfig($this->config)->generate($source);
        }

        return $this->getCollection();
    }

    /**
     * Generate a single code
     *
     * @param string|null $source
     *
     * @return string
     */
    public function generate(?string $source): string
    {
        $count = $this->getNumberOfGeneratedCodes();

        $this->collection($this->checkAttempts($count) < $this->config->getMaxAttempts() ? $source : null);

        return $this->getLastGeneratedCode();
    }

    /**
     * Reset the collection
     *
     * @return void
     */
    private function resetCollection(): void
    {
        $this->collection = [];
    }

    /**
     * Get the last generated code
     *
     * @return string
     */
    private function getLastGeneratedCode(): string
    {
        return !empty($this->getCollection()) ? $this->getCollection()[$this->getNumberOfGeneratedCodes() - 1] : '';
    }

    /**
     * Get the number of generated codes
     *
     * @return int
     */
    private function getNumberOfGeneratedCodes(): int
    {
        return count($this->collection);
    }

    /**
     * Check count attempts
     * Must be at least one and less than maximum attempt
     *
     * @param int $count
     *
     * @return int
     */
    private function checkAttempts(int $count): int
    {
        return min(max(1, $count), $this->config->getMaxAttempts());
    }
}
