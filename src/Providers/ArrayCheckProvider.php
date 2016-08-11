<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use ArrayAccess;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class ArrayCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

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
        $success = is_array($value)
            || (is_object($value) && ($value instanceof ArrayAccess));

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

        if (! is_array($value)) {
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

        if (! is_array($value)) {
            return new Result(false, $message);
        }

        // empty arrays are niether associative or numeric
        if (empty($value)) {
            return new Result(false, $message);
        }

        foreach ($value as $k => $v) {
            if (! is_integer($k)) {
                return new Result(false, $message);
            }
        }

        return new Result(true, '{name} must be an associative array');
    }

    /**
     * Check that $value is a non-empty array.
     *
     * @Check(["isNotEmptyArray", "notEmptyArray"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNotEmpty($value)
    {
        $success = is_array($value) && count($value) > 0;

        return new Result($success, '{name} must be a non-empty array');
    }
}
