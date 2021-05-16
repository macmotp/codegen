<?php

namespace Macmotp\Codegen\Generator;

use Macmotp\Codegen\Config\Config;
use Macmotp\Codegen\Config\Sanitizer;
use Macmotp\Codegen\Scanner\Chunk;
use Macmotp\Codegen\Scanner\Scanner;

/**
 * Class Generator
 *
 * @package Macmotp/Codegen
 */
class Generator
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
     * Iteration
     *
     * @var int
     */
    private int $iteration;

    /**
     * The actual code length without prepend/append
     *
     * @var int
     */
    private int $actualCodeLength;

    /**
     * Collection of codes
     *
     * @var array
     */
    private array $collection;

    /**
     * Generator constructor.
     */
    public function __construct()
    {
        $this->scanner = new Scanner();
        $this->config = new Config();
        $this->sanitizer = new Sanitizer();
        $this->iteration = 0;
        $this->actualCodeLength = 0;
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

    /**
     * Generate a code
     *
     * @param string $source
     *
     * @return $this
     */
    public function generate(string $source): string
    {
        // Scanning and analyzing the source
        $words = explode(' ', $source);
        $this->actualCodeLength = $this->config->getActualCodeLength();

        // Scan the words
        $this->scanner->setCodeLength($this->actualCodeLength)->setIteration($this->iteration++)->scan($words);

        // Generate the code from the chunks
        $code = $this->generateCode();

        // Append random characters if code is less than expected
        // It might happen if there are a lot of short words
        $code = $this->appendRandomCharacters($code, $this->actualCodeLength - strlen($code));

        // Prepend and append from config
        return "{$this->config->getPrependString()}{$code}{$this->config->getAppendString()}";
    }

    /**
     * Generate the actual code
     *
     * @return string
     */
    private function generateCode(): string
    {
        // Get the chunks from scanner
        return implode('', array_map(fn(Chunk $chunk) => $chunk->getChunk(), $this->scanner->getChunks()));
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
