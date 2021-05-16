<?php

namespace Macmotp\Codegen\Scanner;

/**
 * Class Scanner
 *
 * @package Macmotp/Codegen
 */
class Scanner
{
    /**
     * @var int
     */
    private int $iteration;

    /**
     * @var int
     */
    private int $codeLength;

    /**
     * @var int
     */
    private int $numberOfChunks;

    /**
     * @var int
     */
    private int $maxIteration;

    /**
     * @var array|Chunk[]
     */
    private array $chunks;

    /**
     * Scanner constructor.
     */
    public function __construct()
    {
        $this->iteration = 0;
        $this->maxIteration = 1;
        $this->numberOfChunks = 0;
        $this->chunks = [];
    }

    /**
     * Set the iteration
     *
     * @param int $iteration
     *
     * @return $this
     */
    public function setIteration(int $iteration): self
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * Set the code length
     *
     * @param int $codeLength
     *
     * @return $this
     */
    public function setCodeLength(int $codeLength): self
    {
        $this->codeLength = max(1, $codeLength);

        return $this;
    }

    /**
     * Get the number of chunks
     *
     * @return int
     */
    public function getNumberOfChunks(): int
    {
        return $this->numberOfChunks;
    }

    /**
     * Get the maximum iteration
     *
     * @return int
     */
    public function getMaxIteration(): int
    {
        return $this->maxIteration;
    }

    /**
     * Get the generated chunks
     *
     * @return array
     */
    public function getChunks(): array
    {
        return $this->chunks;
    }

    /**
     * Scan words
     *
     * @param array $words
     *
     * @return void
     */
    public function scan(array $words): void
    {
        /** @var array|Chunk[] $chunks */
        $chunks = [];

        // Initiate the chunks
        foreach ($words as $position => $word) {
            $chunks[] = new Chunk($word, strlen($word), $position);
        }

        // Calculate the initial weight distribution
        $numberOfWords = count($words);
        $weightDistribution = $this->getWeightsDistribution($numberOfWords, $this->codeLength);

        // Sort chunks by length, to compare with the initial weight distribution
        $sortedChunks = $this->sortByLength($chunks);
        $distributedChunks = [];

        /** @var Chunk $chunk */
        foreach ($sortedChunks as $index => $chunk) {
            $chunk->setWeight(min($chunk->getLength(), $weightDistribution[$index]));
            // Save only the ones with weight
            if ($chunk->getWeight() >= 1) {
                $this->maxIteration *= $chunk->getMaxNumberOfIterations();
                $distributedChunks[] = $chunk;
            }
        }


        // Scan how many chunks are left
        $this->numberOfChunks = count($distributedChunks);

        // Sort chunks by position asc, to compare with the iteration distribution
        $reversePositionChunks = $this->sortByPositionAsc($distributedChunks);

        $previousLoop = 0;

        // Loop through the chunks and apply iteration based on previous related chunk
        /** @var Chunk $chunk */
        foreach ($reversePositionChunks as $position => $chunk) {
            $chunk->setIteration($position === 0 ? $this->iteration : $previousLoop);
            $previousLoop = $chunk->getLoop();
            $chunk->swap();
        }

        $this->chunks = $this->sortByPositionDesc($reversePositionChunks);
        $this->iteration++;
    }

    /**
     * Gets the distribution of the weight based on the code length and number of words
     *
     * @param int $numberOfWords
     * @param int $codeLength
     *
     * @return array
     */
    public function getWeightsDistribution(int $numberOfWords, int $codeLength): array
    {
        // Set all weights to zero
        $distribution = array_fill(0, $numberOfWords, 0);

        // The first distribution needs a step in advance
        $distribution[0]++;

        for ($i = 0; $i < $codeLength - 1; $i++) {
            $distribution[$i % $numberOfWords]++;
        }

        return $distribution;
    }

    /**
     * Return chunks to array
     * Used for testing purpose
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn (Chunk $chunk) => $chunk->toArray(), $this->getChunks());
    }

    /**
     * Sort chunks by length
     *
     * @param array $chunks
     *
     * @return array
     */
    private function sortByLength(array $chunks): array
    {
        usort($chunks, fn (Chunk $chunkOne, Chunk $chunkTwo) => $chunkTwo->getLength() - $chunkOne->getLength());

        return $chunks;
    }

    /**
     * Sort chunks by position asc
     *
     * @param array $chunks
     *
     * @return array
     */
    private function sortByPositionAsc(array $chunks): array
    {
        return $this->sortByPosition($chunks, 'asc');
    }

    /**
     * Sort chunks by position desc
     *
     * @param array $chunks
     *
     * @return array
     */
    private function sortByPositionDesc(array $chunks): array
    {
        return $this->sortByPosition($chunks, 'desc');
    }

    /**
     * Sort chunks by position
     *
     * @param array $chunks
     * @param string $direction
     *
     * @return array
     */
    private function sortByPosition(array $chunks, string $direction): array
    {
        usort(
            $chunks,
            fn (Chunk $chunkOne, Chunk $chunkTwo) => $direction === 'asc'
            ? $chunkTwo->getPosition() - $chunkOne->getPosition()
            : $chunkOne->getPosition() - $chunkTwo->getPosition()
        );

        return $chunks;
    }
}
