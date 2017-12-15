<?php

namespace Valit\Result;

use Valit\Traits\ContainsResults;

class AssertionResultBag
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
