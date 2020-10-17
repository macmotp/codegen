<?php

namespace Macmotp;

/**
 * Class Codegen
 *
 * @package Macmotp/Codegen
 */
class Codegen
{
    /**
     * The source string where the code will be generated from
     *
     * @var string
     */
    private string $source;

    /**
     *
     * @param string|null $source
     *
     * @return $this
     */
    public function make(string $source = null): self
    {
        $this->source = $source;

        return $this;
    }
}
