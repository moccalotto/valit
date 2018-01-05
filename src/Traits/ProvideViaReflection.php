<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Traits;

use Iterator;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

trait ProvideViaReflection
{
    /**
     * Get the checks for a given method.
     *
     * @param ReflectionMethod $reflectionMethod
     *
     * @return Iterator
     */
    private function checksFromReflectionMethod($reflectionMethod)
    {
        if (strpos($reflectionMethod->getName(), 'check') !== 0) {
            return;
        }

        $doc = $reflectionMethod->getDocComment();

        if (!($doc && preg_match_all('/@Check\((\[?[a-zA-Z0-9_," ]+\]?)\)/S', $doc, $matches))) {
            return;
        }

        $closure = $reflectionMethod->getClosure($this);

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

        foreach ($reflector->getMethods() as $reflectionMethod) {
            foreach ($this->checksFromReflectionMethod($reflectionMethod) as $checkName => $closure) {
                $provides[$checkName] = $closure;
            }
        }

        return $provides;
    }
}
