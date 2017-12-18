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
     * @param string            $message The exception message
     * @param string            $varName The name of the variable that was validated
     * @param mixed             $value   The value validated
     * @param AssertionResult[] $results The results of validating the variable
     */
    public function __construct($message, $varName, $value, array $results)
    {
        $this->value = $value;
        $this->varName = $varName;
        foreach ($results as $result) {
            $this->addAssertionResult($result);
        }

        $value = json_encode($value);

        parent::__construct(implode(PHP_EOL, [
            'Validation failed.',
            "Message: $message",
            "Value {$value} does not pass the following tests",
            '--------------------',
            json_encode(
                $this->errorMessages(),
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            ),
        ]));
    }
}
