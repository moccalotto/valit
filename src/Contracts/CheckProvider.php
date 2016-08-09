<?php

namespace Moccalotto\Valit\Contracts;

interface CheckProvider
{
    /**
     * Return an array of the checks provided by this provider.
     *
     * @return array
     *
     * The returned array has the format
     * [
     *      $checkName => $callback,
     * ]
     */
    public function provides();
}
