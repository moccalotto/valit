<?php

namespace Moccalotto\Valit;

interface CustomChecker
{
    /**
     * @param mixed $value
     *
     * @return Result
     */
    public function check($value);
}
