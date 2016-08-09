<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit;

use UnexpectedValueException;

/**
 * Exception thrown when a value is invalid.
 */
class ValidationException extends UnexpectedValueException
{
    /**
     * @var Result[]
     */
    protected $results;

    /**
     * @var string
     */
    protected $varName;

    /**
     * @var mixed
     */
    protected $value;

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

    /**
     * Get all check results.
     */
    public function getResults()
    {
        return $this->results;
    }

    public function getVarName()
    {
        return $this->varName;
    }

    public function getValue()
    {
        return $this->value;
    }
}
