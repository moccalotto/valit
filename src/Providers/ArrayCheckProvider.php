<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use LogicException;
use Valit\Util\Val;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;
use Valit\Result\AssertionResult as Result;

class ArrayCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $value can be accessed as an array.
     *
     * @Check(["isArrayable", "arrayable", "hasArrayAccess", "arrayAccessible", "isArrayAccessible"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkArrayAccess($value)
    {
        $success = Val::arrayable($value);

        return new Result($success, '{name} must be array accessible');
    }

    /**
     * Check that $value is an array with a continuous 0-based index.
     *
     * It is essentially the same as the `mixed[]` pseudo type.
     *
     * @Check(["isStrictArray", "strictArray"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkStrictArray($value)
    {
        $success = Val::is($value, ['mixed[]']);

        return new Result($success, '{name} must be an array with a continuous 0-based index');
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
            if (!is_integer($key)) {
                return new Result(true, $message);
            }
        }

        return new Result(false, $message);
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

        return new Result(true, '{name} must be an conventional array');
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
        $success = Val::countable($value) && count($value) > 0;

        return new Result($success, '{name} must be a non-empty array');
    }

    /**
     * Check that $value is an empty array or Countable.
     *
     * @Check(["isEmpty", "isEmptyArray"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkEmpty($value)
    {
        $success = Val::countable($value) && count($value) === 0;

        return new Result($success, '{name} must be an empty array');
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

        $success = Val::arrayable($value) && isset($value[$key]);

        return new Result($success, '{name} must have the key {0:raw}', [$key]);
    }
}
