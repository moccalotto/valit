<?php

/**
 * This file is part of the Valit package.
 *
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
