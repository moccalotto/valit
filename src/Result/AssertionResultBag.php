<?php

namespace Valit\Result;

use Valit\Contracts\Result;
use Valit\Traits\ContainsResults;

class AssertionResultBag implements Result
{
    use ContainsResults;

    /**
     * Constructor.
     */
    public function __construct($value, $varName)
    {
        $this->value = $value;
        $this->varName = ((string) $varName) ?: 'value';
    }
}
