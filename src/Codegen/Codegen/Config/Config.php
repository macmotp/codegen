<?php

namespace Macmotp\Codegen\Config;

use Macmotp\Codegen\Exceptions\InvalidCodegenConfigurationException;

class Config
{
    /**
     * The minimum accepted length for a code
     */
    public const MIN_CODE_LENGTH = 3;

    /**
     * The maximum accepted number of attempts
     */
    public const MAX_NUMBER_ATTEMPTS = 10000;

    /**
     * Sanitize levels
     * Levels are inclusive, e.g. the highest level will apply also regex of level low and medium
     * 1. Low: will filter anything is not a letter or a digit
     * 2. Medium/Default: will filter (O - 0 - Q - I - 1 - L) characters
     * 3. High: will filter also (2 - Z - 4 - A - 5 - S - 8 - B - U - V - Y) characters
     */
    public const SANITIZE_LEVEL_LOW = 1;
    public const SANITIZE_LEVEL_MEDIUM = 2;
    public const SANITIZE_LEVEL_HIGH = 3;

    /**
     * The corresponding sanitize regex per level
     */
    public const SANITIZE_REGEX = [
        self::SANITIZE_LEVEL_LOW => '/[^a-zA-Z0-9\s]/',
        self::SANITIZE_LEVEL_MEDIUM => '/([ILOQ]|[^a-zA-Z2-9\s])/',
        self::SANITIZE_LEVEL_HIGH => '/([ABILOQSUVY]|[^c-wC-W3-9\s])/',
    ];

    /**
     * Valid Characters based on sanitize regex
     */
    public const VALID_CHARACTERS = [
        self::SANITIZE_LEVEL_LOW => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        self::SANITIZE_LEVEL_MEDIUM => 'ABCDEFGHJKMNPRSTUVWXYZ23456789',
        self::SANITIZE_LEVEL_HIGH => 'CDEFGHJKMNPRTWX3679',
    ];

    /**
     * The sanitize level
     *
     * @var int
     */
    private int $sanitizeLevel;

    /**
     * The length of the code
     *
     * @var int
     */
    private int $codeLength;

    /**
     * The actual length of the code, considering prepend and append
     *
     * @var int
     */
    private int $actualCodeLength;

    /**
     * The maximum attempts for the collection
     *
     * @var int
     */
    private int $maxAttempts;

    /**
     * The string to prepend
     *
     * @var string
     */
    private string $prependString;

    /**
     * The string to append
     *
     * @var string
     */
    private string $appendString;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->sanitizeLevel = self::SANITIZE_LEVEL_LOW;
        $this->codeLength = 6;
        $this->actualCodeLength = 6;
        $this->maxAttempts = self::MAX_NUMBER_ATTEMPTS;
        $this->prependString = '';
        $this->appendString = '';
    }

    /**
     * Get the code length
     *
     * @return int
     */
    public function getCodeLength(): int
    {
        return $this->codeLength;
    }

    /**
     * Get the actual code length
     *
     * @return int
     */
    public function getActualCodeLength(): int
    {
        return $this->actualCodeLength;
    }

    /**
     * Get the max number of attempts
     *
     * @return int
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * Get the prepend string
     *
     * @return string
     */
    public function getPrependString(): string
    {
        return $this->prependString;
    }

    /**
     * Get the append string
     *
     * @return string
     */
    public function getAppendString(): string
    {
        return $this->appendString;
    }

    /**
     * Get the sanitize level
     *
     * @return int
     */
    public function getSanitizeLevel(): int
    {
        return $this->sanitizeLevel;
    }

    /**
     * Get the sanitize regex
     *
     * @return string
     */
    public function getSanitizeRegex(): string
    {
        return self::SANITIZE_REGEX[$this->sanitizeLevel];
    }

    /**
     * Get the valid characters based on sanitize level
     *
     * @return string
     */
    public function getValidCharacters(): string
    {
        return self::VALID_CHARACTERS[$this->sanitizeLevel];
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
        $this->codeLength = $codeLength;

        $this->checkActualCodeLength();

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
        if ($maxAttempts > self::MAX_NUMBER_ATTEMPTS) {
            throw new InvalidCodegenConfigurationException(
                sprintf('The maximum allowed number of attempts is %d', self::MAX_NUMBER_ATTEMPTS)
            );
        }

        $this->maxAttempts = $maxAttempts;

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
    public function prepend(string $prependString)
    {
        $this->prependString = $prependString;

        $this->checkActualCodeLength();

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
    public function append(string $appendString)
    {
        $this->appendString = $appendString;

        $this->checkActualCodeLength();

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
    public function setSanitizeLevel(int $sanitizeLevel)
    {
        if (! in_array($sanitizeLevel, array_keys(self::SANITIZE_REGEX), true)) {
            throw new InvalidCodegenConfigurationException('Sanitize level not found');
        }

        $this->sanitizeLevel = $sanitizeLevel;

        return $this;
    }

    /**
     * Check the actual code length and throw exception if is less than minimum value required
     *
     * @throws InvalidCodegenConfigurationException
     * @return void
     */
    protected function checkActualCodeLength(): void
    {
        $this->actualCodeLength = $this->codeLength - strlen($this->getPrependString()) - strlen($this->getAppendString());
        if ($this->actualCodeLength < self::MIN_CODE_LENGTH) {
            $message = 'The length of the code must be more than %d characters, check the length of the code length, prepend and append string';

            throw new InvalidCodegenConfigurationException(
                sprintf($message, self::MIN_CODE_LENGTH)
            );
        }
    }
}
