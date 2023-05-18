<?php

namespace Freezemage\Config\Internal;

final class ChainedValue
{
    /** @var array<array-key, string> */
    public array $chain;
    /** @var mixed */
    public $value;

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