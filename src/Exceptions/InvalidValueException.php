<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Exceptions;

use UnexpectedValueException;
use Valit\Result\AssertionResult;
use Valit\Traits\ContainsResults;

/**
 * Exception to throw when one or more assertions failed.
 */
class InvalidValueException extends UnexpectedValueException
{
    use ContainsResults;

    /**
     * Constructor.
     *
     * @param string            $varName The name of the variable that was validated
     * @param mixed             $value   The value validated
     * @param AssertionResult[] $results The results of validating the variable
     */
    public function __construct($varName, $value, array $results)
    {
        $this->value = $value;
        $this->varName = $varName;
        foreach ($results as $result) {
            $this->addAssertionResult($result);
        }

        $bullets = $this->errorBullets();

        parent::__construct(implode(PHP_EOL, [
            "Validation of {$this->varName} failed the following tests:",
            $bullets,
        ]));
    }

    /**
     * @return string[]
     */
    protected function errorBullets()
    {
        return implode(
            PHP_EOL,
            array_map(function ($error) {
                return " * $error.";
            }, $this->errorMessages())
        );
    }
}
