<?php

namespace Macmotp\Codegen\Tests\Unit\Scanner;

use Macmotp\Codegen\Exceptions\InvalidCodegenMappingException;
use Macmotp\Codegen\Scanner\Mapping;
use PHPUnit\Framework\TestCase;

/**
 * Class MappingTest
 *
 * @package Macmotp\Codegen\Tests\Unit\Scanner
 * @group Scanner
 */
class MappingTest extends TestCase
{
    private Mapping $mapping;

    protected function setUp(): void
    {
        $this->mapping = new Mapping();
    }

    public function testMappingThrowsExceptionIfIndexIsNotAvailable()
    {
        $this->expectException(InvalidCodegenMappingException::class);
        $this->expectExceptionMessage('Cannot identify index for chunk length 1, weight 1 and iteration 10');

        $this->mapping->getIndexes(1, 1, 10);
    }

    public function testMappingThrowsExceptionIfIterationIsNotAvailable()
    {
        $this->expectException(InvalidCodegenMappingException::class);
        $this->expectExceptionMessage('Cannot identify iterations for chunk length 1 and weight 2');

        $this->mapping->getMaxNumberOfIterations(1, 2);
    }

    /**
     * @dataProvider listOfIndexesArrays
     *
     * @param int $weight
     * @param int $iteration
     * @param int $chunkLength
     * @param array $result
     *
     * @throws InvalidCodegenMappingException
     */
    public function testMappingGetsTheIndexesArray(int $weight, int $iteration, int $chunkLength, array $result)
    {
        $this->assertEquals($result, $this->mapping->getIndexes($chunkLength, $weight, $iteration));
    }

    /**
     * @dataProvider listOfMaxIterations
     *
     * @param int $weight
     * @param int $chunkLength
     * @param int $result
     *
     * @throws InvalidCodegenMappingException
     */
    public function testMappingGetsTheMaxNumberOfIterations(int $weight, int $chunkLength, int $result)
    {
        $this->assertEquals($result, $this->mapping->getMaxNumberOfIterations($chunkLength, $weight));
    }

    /**
     * List of indexes to calculate
     *
     * @return array[]
     */
    public function listOfIndexesArrays(): array
    {
        return [
            'weight 1, iteration 0, chunk length 1' => [1, 0, 1, [1]],
            'weight 1, iteration 0, chunk length 2' => [1, 0, 2, [1, 0]],
            'weight 1, iteration 1, chunk length 2' => [1, 1, 2, [0, 1]],
            'weight 2, iteration 0, chunk length 2' => [2, 0, 2, [1, 1]],
            'weight 2, iteration 0, chunk length 3' => [2, 0, 3, [1, 1, 0]],
            'weight 2, iteration 1, chunk length 3' => [2, 1, 3, [1, 0, 1]],
            'weight 2, iteration 2, chunk length 3' => [2, 2, 3, [0, 1, 1]],
            'weight 6, iteration 10, chunk length 9' => [6, 10, 9, [1, 1, 1, 0, 1, 1, 1, 0, 0]],
        ];
    }

    /**
     * List of max iterations to calculate
     *
     * @return array[]
     */
    public function listOfMaxIterations(): array
    {
        return [
            'weight 1, chunk length 1' => [1, 1, 1],
            'weight 1, chunk length 2' => [1, 2, 2],
            'weight 2, chunk length 2' => [2, 2, 1],
            'weight 2, chunk length 3' => [2, 3, 3],
            'weight 6, chunk length 9' => [6, 9, 84],
        ];
    }
}
