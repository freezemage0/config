<?php

namespace Freezemage\Config\Internal;

final class ChainedValue
{
    /** @var array<int, array-key> */
    public array $chain;
    /** @var mixed */
    public $value;

    /**
     * @param array<int, array-key> $chain
     * @param mixed $value
     */
    public function __construct(array $chain, $value)
    {
        $this->chain = $chain;
        $this->value = $value;
    }

    public function getFullName(): string
    {
        return implode('.', $this->chain);
    }
}