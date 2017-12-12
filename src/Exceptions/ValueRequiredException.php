<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Exceptions;

use LogicException;
use Valit\Traits\ContainsResults;

class ValueRequiredException extends LogicException
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param array  $results
     */
    public function __construct($message)
    {
        parent::__construct(implode(PHP_EOL, [
            'Validation failed.',
            "Message: $message",
        ]));
    }
}
