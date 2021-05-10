<?php

namespace Macmotp\Codegen\Tests\Unit\Scanner;

use Macmotp\Codegen\Scanner\Chunk;
use PHPUnit\Framework\TestCase;

/**
 * Class ChunkTest
 *
 * @package Macmotp\Codegen\Tests\Unit\Scanner
 * @group Scanner
 */
class ChunkTest extends TestCase
{
    /**
     * @dataProvider listOfCharactersToSwap
     *
     * @param string $word
     * @param int $weight
     * @param int $iteration
     * @param string $result
     * @param int $maxIteration
     * @param int $loop
     */
    public function testChunksCanSwap(string $word, int $weight, int $iteration, string $result, int $maxIteration, int $loop)
    {
        $chunk = new Chunk($word, $weight, 0, $iteration);
        $chunk->swap();

        $this->assertSame($result, $chunk->getChunk());
        $this->assertSame($maxIteration, $chunk->getMaxNumberOfIterations());
        $this->assertSame($loop, $chunk->getLoop());
    }

    /**
     * List of characters chunks to swap
     *
     * @return array[]
     */
    public function listOfCharactersToSwap(): array
    {
        return [
            'character 0, weight 1, iteration 0' => ['', 1, 0, '', 1, 0],
            'character 1, weight -1, iteration 0' => ['A', -1, 0, 'A', 1, 0],
            'character 1, weight 1, iteration 0' => ['A', 1, 0, 'A', 1, 0],
            'character 1, weight 1, iteration 1' => ['A', 1, 1, 'A', 1, 1],
            'character 1, weight 1, iteration 10' => ['A', 1, 10, 'A', 1, 10],
            'character 1, weight 2, iteration 0' => ['A', 2, 0, 'A', 1, 0],
            'character 2, weight 2, iteration 0' => ['AB', 2, 0, 'AB', 1, 0],
            'character 2, weight 2, iteration 1' => ['AB', 2, 1, 'AB', 1, 1],
            'character 3, weight 2, iteration 0' => ['ABC', 2, 0, 'AB', 3, 0],
            'character 3, weight 2, iteration 1' => ['ABC', 2, 1, 'AC', 3, 0],
            'character 3, weight 2, iteration 2' => ['ABC', 2, 2, 'BC', 3, 0],
            'character 3, weight 2, iteration 3' => ['ABC', 2, 3, 'AB', 3, 1],
            'character 3, weight 2, iteration 4' => ['ABC', 2, 4, 'AC', 3, 1],
            'character 7, weight 4, iteration 9' => ['ABCDEFG', 4, 9, 'ACFG', 29, 0],
            'character 8, weight 12, iteration 0' => ['ABCDEFAB', 12, 0, 'ABCDEFAB', 1, 0],
            'character 12, weight 4, iteration 0' => ['ABCDEFABCDEF', 4, 0, 'ABCD', 189, 0],
        ];
    }
}
