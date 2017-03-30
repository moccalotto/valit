<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use Countable;
use ArrayAccess;
use LogicException;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class ArrayCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    protected function hasArrayAccess($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof ArrayAccess));
    }

    protected function isCountable($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof Countable));
    }

    /**
     * Check that $value can be accessed as an array.
     *
     * @Check(["hasArrayAccess", "arrayAccessible"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkArrayAccess($value)
    {
        $success = $this->hasArrayAccess($value);

        return new Result($success, '{name} must be array accessible');
    }

    /**
     * Check that $value is an associative array - i.e. that it contains no integer-keys.
     *
     * @Check(["isAssociative", "associative", "isAssociativeArray", "associativeArray"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkAssociative($value)
    {
        $message = '{name} must be an associative array';

        if (!is_array($value)) {
            return new Result(false, $message);
        }

        // empty arrays are niether associative or numeric
        if (empty($value)) {
            return new Result(false, $message);
        }

        foreach (array_keys($value) as $key) {
            if (is_integer($key)) {
                return new Result(false, $message);
            }
        }

        return new Result(true, '{name} must be an associative array');
    }

    /**
     * Check that $value is a conventional array - i.e. that it contains only integer-keys.
     *
     * @Check(["hasNumericIndex", "isConventionalArray", "conventionalArray", "isNotAssociative", "notAssociative"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNumericIndex($value)
    {
        $message = '{name} must be a conventional array';

        if (!is_array($value)) {
            return new Result(false, $message);
        }

        // empty arrays are niether associative or numeric
        if (empty($value)) {
            return new Result(false, $message);
        }

        foreach (array_keys($value) as $key) {
            if (!is_integer($key)) {
                return new Result(false, $message);
            }
        }

        return new Result(true, '{name} must be an associative array');
    }

    /**
     * Check that $value is a non-empty array or Countable.
     *
     * @Check(["isNotEmptyArray", "notEmptyArray", "isNotEmpty", "notEmpty"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNotEmpty($value)
    {
        $success = $this->isCountable($value) && count($value) > 0;

        return new Result($success, '{name} must be a non-empty array');
    }

    /**
     * Check that $value is an array with unique values.
     *
     * @Check(["hasUniqueValues", "uniqueValues"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUniqueValues($value)
    {
        $success = is_array($value) && count($value) == count(array_unique($value));

        return new Result($success, '{name} must be an array with unique values');
    }

    /**
     * Check that $value is an array or ArrayAccess that has the given $key.
     *
     * @Check(["hasKey", "keyExists"])
     *
     * @param mixed      $value
     * @param string|int $key
     *
     * @return Result
     */
    public function checkKeyExists($value, $key)
    {
        if (!(is_string($key) || is_int($key))) {
            throw new LogicException('$key must be int or string');
        }

        $success = $this->hasArrayAccess($value) && isset($value[$key]);

        return new Result($success, '{name} must have the key {0:raw}', [$key]);
    }
}
