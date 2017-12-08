<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Result\SingleAssertionResult;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class JsonCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $value is valid json.
     *
     * @Check(["isValidJson", "validJson", "isJson"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkIsJson($value)
    {
        $json = is_string($value) ? json_decode($value, true) : false;

        // real json must be object or array - scalar values are not allowed according to the specs
        return new SingleAssertionResult(is_array($json), '{name} must be valid json');
    }
}
