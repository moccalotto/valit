<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit;

use UnexpectedValueException;

/**
 * Exception thrown when a value is invalid.
 */
class ValidationException extends UnexpectedValueException
{
    use Traits\ContainsResults;

    /**
     * Constructor.
     *
     * @param string $message
     * @param array  $results
     */
    public function __construct($message, $varName, $value, array $results)
    {
        $this->value = $value;
        $this->varName = $varName;
        $this->results = $results;

        parent::__construct($message);
    }
}
