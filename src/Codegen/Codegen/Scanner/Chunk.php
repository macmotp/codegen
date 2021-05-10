<?php

namespace Macmotp\Codegen\Scanner;

use Macmotp\Codegen\Exceptions\InvalidCodegenMappingException;

/**
 * Class Chunk
 *
 * @package Macmotp/Codegen
 */
class Chunk
{
    private Mapping $mapping;
    private string $word;
    private string $chunk;
    private array $splits;
    private int $iteration;
    private int $maxNumberOfIterations;
    private int $loop;
    private int $weight;
    private int $length;
    private int $position;

    /**
     * Chunk constructor.
     *
     * @param string $word
     * @param int $weight
     * @param int $position
     * @param int $iteration
     */
    public function __construct(string $word, int $weight, int $position = 0, int $iteration = 0)
    {
        $this->mapping = new Mapping();
        $this->word = substr($word, 0, $this->mapping::MAX_INDEX);
        $this->splits = str_split($this->word);
        $this->length = strlen($this->word);
        $this->position = $position;
        $this->iteration = $iteration;
        $this->weight = min(max(1, min($weight, $this->mapping::MAX_INDEX)), $this->length);
        $this->calculateMaxNumberOfIterations();
        $this->resetChunk();
    }

    /**
     * Get the chunk
     *
     * @return string
     */
    public function getChunk(): string
    {
        return $this->chunk;
    }

    /**
     * Get the position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Get the loop
     *
     * @return int
     */
    public function getLoop(): int
    {
        return $this->loop;
    }

    /**
     * Get the length
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get the weight
     *
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * Get the maximum possible number of iterations
     *
     * @return int
     */
    public function getMaxNumberOfIterations(): int
    {
        return $this->maxNumberOfIterations;
    }

    /**
     * Set the iteration
     *
     * @param int $iteration
     *
     * @return self
     */
    public function setIteration(int $iteration): self
    {
        $this->iteration = $iteration;
        $this->resetIteration();

        return $this;
    }

    /**
     * Set the weight
     *
     * @param int $weight
     *
     * @return self
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        $this->calculateMaxNumberOfIterations();

        return $this;
    }

    /**
     * Swap a chunk
     *
     * @return void
     */
    public function swap(): void
    {
        $this->resetChunk();

        try {
            $indexes = $this->mapping->getIndexes($this->length, $this->weight, $this->iteration);
        } catch (InvalidCodegenMappingException $e) {
            $this->chunk = $this->word;

            return;
        }

        foreach ($this->splits as $index => $split) {
            $this->chunk .= $indexes[$index] ? $split : '';
        }
    }

    /**
     * Transform to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'chunk' => $this->getChunk(),
            'weight' => $this->getWeight(),
            'position' => $this->getPosition(),
            'loop' => $this->getLoop(),
            'max_iteration' => $this->getMaxNumberOfIterations(),
        ];
    }

    /**
     * Reset the chunk
     *
     * @return void
     */
    private function resetChunk(): void
    {
        $this->chunk = '';
    }

    /**
     * Calculate the max number of iterations
     *
     * @return void
     */
    private function calculateMaxNumberOfIterations(): void
    {
        try {
            $this->maxNumberOfIterations = $this->mapping->getMaxNumberOfIterations($this->length, $this->weight);
            $this->resetIteration();
        } catch (InvalidCodegenMappingException $e) {
            $this->weight = 0;
            $this->maxNumberOfIterations = 1;
            $this->loop = $this->iteration;
        }
    }

    /**
     * Reset the iteration
     *
     * @return void
     */
    private function resetIteration(): void
    {
        $this->loop = floor($this->iteration / $this->maxNumberOfIterations);
        $this->iteration = $this->iteration % $this->maxNumberOfIterations;
    }
}
