<?php

namespace Macmotp\Codegen\Config;

/**
 * Class Sanitizer
 *
 * @package Macmotp/Codegen
 */
class Sanitizer
{
    /**
     * Maximum Source Length Accepted
     */
    public const MAXIMUM_SOURCE_LENGTH_ACCEPTED = 255;

    /**
     * Not meaningful words to discard
     */
    protected const FILTERED_WORDS = [
        'AT',
        'BY',
        'FRM',
        'FROM',
        'FOR',
        'FR',
        'IN',
        'INTO',
        'INT',
        'NT',
        'OF',
        'ON',
        'THAT',
        'THT',
        'THIS',
        'THS',
        'TH',
        'THE',
        'TO',
    ];

    /**
     * The source words
     *
     * @var string
     */
    protected string $source;

    /**
     * The sanitize regex
     *
     * @var string
     */
    protected string $sanitizeRegex;

    /**
     * The sanitized words
     *
     * @var array
     */
    protected array $words;

    /**
     * Sanitize the string
     *
     * @param string $source
     *
     * @return array
     */
    public function sanitize(string $source): array
    {
        $this->source = $source;

        $this->cut()
            ->trim()
            ->normalize()
            ->capitalize()
            ->applyRegex()
            ->trim()
            ->filter();

        return $this->words;
    }

    /**
     * Set sanitize regex
     *
     * @param string $sanitizeRegex
     *
     * @return $this
     */
    public function setSanitizeRegex(string $sanitizeRegex): self
    {
        $this->sanitizeRegex = $sanitizeRegex;

        return $this;
    }

    /**
     * Cut the source if it is too long
     *
     * @return self
     */
    private function cut(): self
    {
        $this->source = substr($this->source, 0, self::MAXIMUM_SOURCE_LENGTH_ACCEPTED);

        return $this;
    }

    /**
     * Trim spaces
     *
     * @return self
     */
    private function trim(): self
    {
        $this->source = trim(preg_replace('!\s+!', ' ', $this->source));

        return $this;
    }

    /**
     * Normalize non latin characters
     *
     * @link https://www.php.net/manual/en/transliterator.transliterate.php
     *
     * @return self
     */
    private function normalize(): self
    {
        $this->source = transliterator_transliterate('Any-Latin; Latin-ASCII;', $this->source);

        return $this;
    }

    /**
     * Capitalize all characters
     *
     * @return self
     */
    private function capitalize(): self
    {
        $this->source = strtoupper($this->source);

        return $this;
    }

    /**
     * Filter unwanted characters based on sanitize regex
     *
     * @return self
     */
    private function applyRegex(): self
    {
        $this->source = preg_replace($this->sanitizeRegex, '', $this->source);

        return $this;
    }

    /**
     * Apply filtered words
     *
     * @return self
     */
    private function filter(): self
    {
        $this->words = explode(' ', $this->source);
        $this->words = array_values(array_filter($this->words, function ($word) {
            return ! in_array($word, self::FILTERED_WORDS, true);
        }));

        return $this;
    }
}
