<?php

namespace Macmotp;

use Macmotp\Codegen\Config\Config;
use Macmotp\Codegen\Config\Sanitizer;
use Macmotp\Codegen\Scanner\Chunk;
use Macmotp\Codegen\Scanner\Scanner;

/**
 * Class Codegen
 *
 * @package Macmotp/Codegen
 */
class Codegen
{
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
     * The scanner
     *
     * @var Scanner
     */
    private Scanner $scanner;

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
        $this->scanner = new Scanner();
        $this->config = new Config();
        $this->sanitizer = new Sanitizer();
        $this->collection = [];
    }

    /**
     * Override the default config
     *
     * @param Config $config
     *
     * @return $this
     */
    public function withConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function collection(string $source, int $number): array
    {
        // Sanitize, scanning and analyzing the source
        $words = $this->sanitizer->setSanitizeRegex($this->config->getSanitizeRegex())->sanitize($source);
        $codeLength = $this->config->getActualCodeLength();

        for ($i = 0; $i < $number; $i++) {
            $this->processWords($words, $i);
            $code = $this->generateCode();
            $length = strlen($code);
            // Append random characters if code is less than expected
            // It might happen if there are a lot of short words
            $code = $this->appendRandomCharacters($code, $codeLength - $length);

            // Prepend and append from config
            $this->collection[] = "{$this->config->getPrependString()}{$code}{$this->config->getAppendString()}";
        }

        return $this->collection;
    }

    /**
     * Generate a single code
     *
     * @param string $source
     *
     * @return string
     */
    public function generate(string $source): string
    {
        $this->collection($source, 1);

        return $this->collection[0];
    }

    /**
     * Process the words
     *
     * @param array $words
     * @param int $iteration
     *
     * @return void
     */
    private function processWords(array $words, int $iteration): void
    {
        $this->scanner->setIteration($iteration)->setCodeLength($this->config->getActualCodeLength())->scan($words);
    }

    /**
     * Generate the actual code
     *
     * @return string
     */
    private function generateCode(): string
    {
        // Get the chunks from scanner
        return implode('', array_map(fn (Chunk $chunk) => $chunk->getChunk(), $this->scanner->getChunks()));
    }

    /**
     * Append random characters
     *
     * @param string $generated
     * @param int $differenceInLength
     *
     * @return string
     */
    private function appendRandomCharacters(string $generated, int $differenceInLength): string
    {
        if ($differenceInLength <= 0) {
            return $generated;
        }

        return $generated . substr(str_shuffle($this->config->getValidCharacters()), 0, $differenceInLength);
    }
}
