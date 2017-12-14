<?php

namespace Valit\Result;

use Valit\Traits\ContainsResults;

class AssertionResultBag
{
    use ContainsResults;

    /**
     * @var bool
     *
     * @internal
     */
    public $throwOnFailure;

    /**
     * Constructor.
     */
    public function __construct($value, $varName, $throwOnFailure)
    {
        $this->value = $value;
        $this->varName = ((string) $varName) ?: 'value';
        $this->throwOnFailure = $throwOnFailure;
    }
}
