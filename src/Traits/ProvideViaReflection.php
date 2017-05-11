<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Traits;

use Iterator;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

trait ProvideViaReflection
{
    /**
     * Get the checks for a given method.
     *
     * @param ReflectionMethod $method
     *
     * @return Iterator
     */
    protected function checksFor(ReflectionMethod $method)
    {
        if (strpos($method->getName(), 'check') !== 0) {
            return;
        }

        $doc = $method->getDocComment();

        if (!($doc && preg_match_all('/@Check\((\[?[a-zA-Z0-9_," ]+\]?)\)/', $doc, $matches))) {
            return;
        }

        $closure = $method->getClosure($this);

        foreach ($matches[1] as $checksString) {
            $jsonResult = json_decode($checksString);

            if ($jsonResult === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new LogicException(sprintf(
                    'Could not parse the check "%s"',
                    $checksString
                ));
            }

            $checkNames = (array) $jsonResult;

            foreach ($checkNames as $checkName) {
                yield $checkName => $closure;
            }
        }
    }

    /**
     * Return all the checks provided by this checkprovider.
     *
     * @return array Associative array of [checkName => checkClosure]
     */
    public function provides()
    {
        $reflector = new ReflectionClass($this);

        $provides = [];

        foreach ($reflector->getMethods() as $method) {
            foreach ($this->checksFor($method) as $checkName => $closure) {
                $provides[$checkName] = $closure;
            }
        }

        return $provides;
    }
}
