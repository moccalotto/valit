<?php

namespace Valit\Contracts;

interface CustomChecker
{
    /**
     * @param mixed $value
     *
     * @return \Valit\Result\AssertionResult
     */
    public function check($value);
}
