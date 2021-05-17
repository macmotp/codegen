<?php

namespace Macmotp\Codegen\Tests\Unit\Scanner;

use Macmotp\Codegen\Scanner\Scanner;
use PHPUnit\Framework\TestCase;

/**
 * Class ScannerTest
 *
 * @package Macmotp\Codegen\Tests\Unit\Scanner
 * @group Scanner
 */
class ScannerTest extends TestCase
{
    private Scanner $scanner;

    protected function setUp(): void
    {
        $this->scanner = new Scanner();
    }

    /**
     * @dataProvider listOfWordsToScan
     *
     * @param array $words
     * @param int $codeLength
     * @param int $iteration
     * @param array $chunks
     * @param int $numberOfChunks
     * @param int $maxIteration
     */
    public function testScannerCalculateChunks(array $words, int $codeLength, int $iteration, array $chunks, int $numberOfChunks, int $maxIteration)
    {
        $this->scanner->setCodeLength($codeLength)->setIteration($iteration)->scan($words);

        $this->assertSame($chunks, $this->scanner->toArray());
        $this->assertSame($numberOfChunks, $this->scanner->getNumberOfChunks());
        $this->assertSame($maxIteration, $this->scanner->getMaxIteration());
    }

    /**
     * @dataProvider listOfWeightsToCalculate
     *
     * @param int $numberOfWords
     * @param int $codeLength
     * @param array $weights
     */
    public function testScannerCalculatesWeights(int $numberOfWords, int $codeLength, array $weights)
    {
        $this->assertSame($weights, $this->scanner->getWeightsDistribution($numberOfWords, $codeLength));
        $this->assertTrue(array_sum($weights) === $codeLength);
    }

    /**
     * List of words to scan
     *
     * @return array[]
     */
    public function listOfWordsToScan(): array
    {
        return [
            'words 1, code length 1, iteration 0' => [['A'], 1, 0, [
                [
                    'chunk' => 'A',
                    'weight' => 1,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 1,
                ],
            ], 1, 1],
            'words 1, code length 4, iteration 0' => [['ABCDE'], 4, 0, [
                [
                    'chunk' => 'ABCD',
                    'weight' => 4,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 5,
                ],
            ], 1, 5],
            'words 1, code length 4, iteration 1' => [['ABCDE'], 4, 1, [
                [
                    'chunk' => 'ABCE',
                    'weight' => 4,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 5,
                ],
            ], 1, 5],
            'words 1, code length 3, iteration 0' => [['A'], 3, 0, [
                [
                    'chunk' => 'A',
                    'weight' => 1,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 1,
                ],
            ], 1, 1],
            'words 2, code length 4, iteration 0' => [['ABC', 'DE'], 4, 0, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'D',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 0,
                    'max_iteration' => 2,
                ],
            ], 2, 2],
            'words 2, code length 4, iteration 1' => [['ABC', 'DE'], 4, 1, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'E',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 0,
                    'max_iteration' => 2,
                ],
            ], 2, 2],
            'words 2, code length 4, iteration 2' => [['ABC', 'DE'], 4, 2, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 1,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'D',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 1,
                    'max_iteration' => 2,
                ],
            ], 2, 2],
            'words 2, code length 4, iteration 3' => [['ABC', 'DE'], 4, 3, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 1,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'E',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 1,
                    'max_iteration' => 2,
                ],
            ], 2, 2],
            'words 2, code length 4, iteration 4' => [['ABC', 'DE'], 4, 4, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 2,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'D',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 2,
                    'max_iteration' => 2,
                ],
            ], 2, 2],
            'words 3, code length 6, iteration 0' => [['ABCD', 'DEF', 'GHI'], 6, 0, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 4,
                ],
                [
                    'chunk' => 'DE',
                    'weight' => 2,
                    'position' => 1,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
                [
                    'chunk' => 'G',
                    'weight' => 1,
                    'position' => 2,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
            ], 3, 36],
            'words 3, code length 6, iteration 1' => [['ABCD', 'DEF', 'GHI'], 6, 1, [
                [
                    'chunk' => 'ABC',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 4,
                ],
                [
                    'chunk' => 'DE',
                    'weight' => 2,
                    'position' => 1,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
                [
                    'chunk' => 'H',
                    'weight' => 1,
                    'position' => 2,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
            ], 3, 36],
            'words 3, code length 6, iteration 10' => [['ABCD', 'DEF', 'GHI'], 6, 11, [
                [
                    'chunk' => 'ABD',
                    'weight' => 3,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 4,
                ],
                [
                    'chunk' => 'DE',
                    'weight' => 2,
                    'position' => 1,
                    'loop' => 1,
                    'max_iteration' => 3,
                ],
                [
                    'chunk' => 'I',
                    'weight' => 1,
                    'position' => 2,
                    'loop' => 3,
                    'max_iteration' => 3,
                ],
            ], 3, 36],
            'words 5, code length 10, iteration 6' => [['ABC', 'D', 'GHI', 'ABCD', 'ABC'], 10, 6, [
                [
                    'chunk' => 'AB',
                    'weight' => 2,
                    'position' => 0,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
                [
                    'chunk' => 'D',
                    'weight' => 1,
                    'position' => 1,
                    'loop' => 0,
                    'max_iteration' => 1,
                ],
                [
                    'chunk' => 'GH',
                    'weight' => 2,
                    'position' => 2,
                    'loop' => 0,
                    'max_iteration' => 3,
                ],
                [
                    'chunk' => 'ACD',
                    'weight' => 3,
                    'position' => 3,
                    'loop' => 0,
                    'max_iteration' => 4,
                ],
                [
                    'chunk' => 'AB',
                    'weight' => 2,
                    'position' => 4,
                    'loop' => 2,
                    'max_iteration' => 3,
                ],
            ], 5, 108],
        ];
    }

    /**
     * List of weights to calculate
     *
     * @return array[]
     */
    public function listOfWeightsToCalculate(): array
    {
        return [
            'number of chunks 1, code length 1' => [1, 1, [1]],
            'number of chunks 1, code length 2' => [1, 2, [2]],
            'number of chunks 1, code length 10' => [1, 10, [10]],
            'number of chunks 2, code length 1' => [2, 1, [1, 0]],
            'number of chunks 2, code length 2' => [2, 2, [2, 0]],
            'number of chunks 2, code length 3' => [2, 3, [2, 1]],
            'number of chunks 2, code length 10' => [2, 10, [6, 4]],
            'number of chunks 2, code length 20' => [2, 20, [11, 9]],
            'number of chunks 4, code length 1' => [4, 1, [1, 0, 0, 0]],
            'number of chunks 4, code length 2' => [4, 2, [2, 0, 0, 0]],
            'number of chunks 4, code length 3' => [4, 3, [2, 1, 0, 0]],
            'number of chunks 4, code length 4' => [4, 4, [2, 1, 1, 0]],
            'number of chunks 4, code length 5' => [4, 5, [2, 1, 1, 1]],
            'number of chunks 4, code length 6' => [4, 6, [3, 1, 1, 1]],
            'number of chunks 4, code length 7' => [4, 7, [3, 2, 1, 1]],
            'number of chunks 10, code length 3' => [10, 3, [2, 1, 0, 0, 0, 0, 0, 0, 0, 0]],
        ];
    }
}
