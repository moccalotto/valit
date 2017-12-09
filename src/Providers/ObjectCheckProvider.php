<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use ReflectionClass;
use Valit\Result\AssertionResult as Result;
use InvalidArgumentException;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class ObjectCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if is object or class.
     *
     * @Check(["isObjectOrClass", "objectOrClass"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkObjectOrClass($value)
    {
        $success = is_object($value)
            || (is_string($value) && class_exists($value));

        return new Result($success, '{name} must be an object or a fully qualified class name');
    }

    /**
     * Check if is class name.
     *
     * @Check(["isClassName", "isClass", "className", "isOfClass"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkClassName($value)
    {
        $success = is_string($value) && class_exists($value);

        return new Result($success, '{name} must be a fully qualified class name');
    }

    /**
     * Check if value is the name of an interface.
     *
     * @Check(["isInterfaceName", "isInterface", "interfaceName"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkInterfaceName($value)
    {
        $success = is_string($value) && interface_exists($value);

        return new Result($success, '{name} must be a fully qualified interface name');
    }

    /**
     * Check if value is the fqcn of a trait.
     *
     * @Check(["isTraitName", "isTrait", "traitName"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkTraitName($value)
    {
        $success = is_string($value) && trait_exists($value);

        return new Result($success, '{name} must be a fully qualified trait name');
    }

    /**
     * Check if $value is an instance of $fqcn.
     *
     * @Check(["isInstanceOf"])
     *
     * @param mixed  $value
     * @param string $fqcn
     *
     * @return Result
     */
    public function checkInstanceOf($value, $fqcn)
    {
        if (!is_string($fqcn)) {
            throw new InvalidArgumentException('$fqcn must be a string');
        }

        $success = is_a($value, $fqcn);

        return new Result($success, '{name} must be an instance of {0}', [$fqcn]);
    }

    /**
     * Check if value implements the $fqcn interface.
     *
     * @Check(["isImplementing", "implements"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkImplements($value, $fqcn)
    {
        if (!(is_string($fqcn) && interface_exists($fqcn))) {
            if (is_scalar($fqcn)) {
                throw new InvalidArgumentException(sprintf(
                    '$fqcn "%s" is not valid. It must be a string and a valid interface fqcn',
                    $fqcn
                ));
            }
            throw new InvalidArgumentException('$fqcn must be a string and a valid interface fqcn');
        }

        $finalSuccess = $partialSuccess = is_object($value) || (is_scalar($value) && class_exists($value));

        if ($partialSuccess) {
            $refClass = new ReflectionClass($value);
            $finalSuccess = $refClass->implementsInterface($fqcn);
        }

        return new Result($finalSuccess, '{name} must be an object or class fqcn that implements the interface {0}');
    }

    /**
     * Check if value is an object or class that has the given method.
     *
     * @Check("hasMethod")
     *
     * @param mixed  $value
     * @param string $methodName
     *
     * @return Result
     */
    public function checkHasMethod($value, $methodName)
    {
        if (!is_string($methodName)) {
            throw new InvalidArgumentException('$methodName must be a string and an identifier');
        }

        $success = (is_string($value) || is_object($value)) && method_exists($value, $methodName);

        return new Result($success, '{name} must have a method called {0}', [$methodName]);
    }

    /**
     * Check if value is an object or class that has the given propert.
     *
     * @Check("hasProperty")
     *
     * @param mixed  $value
     * @param string $property
     *
     * @return Result
     */
    public function checkHasProperty($value, $property)
    {
        if (!is_string($property)) {
            throw new InvalidArgumentException('$property must be a string and an identifier');
        }

        $success = (is_string($value) || is_object($value)) && property_exists($value, $property);

        return new Result($success, '{name} must have a property called {0}', [$property]);
    }

    /**
     * Check if value is an object or class that uses the given trait.
     *
     * @Check("hasTrait")
     *
     * @param mixed  $value
     * @param string $traitName
     *
     * @return Result
     */
    public function checkHasTrait($value, $traitName)
    {
        $finalSuccess = $partialSuccess = (is_object($value) || class_exists($value));

        if ($partialSuccess) {
            $traits = (new ReflectionClass($value))->getTraitNames();
            $finalSuccess = in_array($traitName, $traits, true);
        }

        return new Result($finalSuccess, '{name} must use a trait called {0}', [$traitName]);
    }
}
