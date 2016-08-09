<?php

namespace Moccalotto\Valit\Providers;

use Moccalotto\Valit\Result;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class JsonCheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $value is valid json
     *
     * @Check(["isValidJson", "validJson", "isJson"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsJson($value)
    {
        $json = is_string($value) ? json_decode($value, true) : false;

        // real json must be object or array - scalar values are not allowed according to the specs
        return new Result(is_array($json), '{name} must be valid json');
    }
}
