<?php

namespace Valit\Contracts;

interface Result
{
    /**
     * Did the check (or checks) succeed.
     *
     * @return bool
     */
    public function success();
}
