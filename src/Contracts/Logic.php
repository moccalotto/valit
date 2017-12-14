<?php

namespace Valit\Contracts;

use Valit\Result\AssertionResult;

interface Logic
{
    /**
     * Execute the logic.
     *
     * @param bool  $withValue
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function execute($hasValue = false, $value = null);

    /**
     * Execute the logic with a given value.
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function withValue($value);

    /**
     * Execute the logic without a value.
     *
     * @return AssertionResult
     */
    public function withoutValue();

    /**
     * The requirements of this logic.
     *
     * It can be 'none', 'simple value' or 'container'
     *
     * @return string
     */
    public function requirements();
}
