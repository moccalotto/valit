<?php

namespace Valit\Contracts;

interface CustomChecker
{
    /**
     * @param mixed $value
     *
     * @return \Valit\Result\SingleAssertionResult
     */
    public function check($value);
}
