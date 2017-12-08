<?php

namespace Valit\Contracts;

interface CustomChecker
{
    /**
     * @param mixed $value
     *
     * @return Result
     */
    public function check($value);
}
