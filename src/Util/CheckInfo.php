<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use Closure;
use ReflectionFunction;

class CheckInfo
{
    /**
     * @var string
     *
     * @internal
     */
    public $name;

    /**
     * @var string
     *
     * @internal
     */
    public $description;

    /**
     * @var string[]
     *
     * @internal
     */
    public $aliases;

    /**
     * @var string
     *
     * @internal
     */
    public $paramlist;

    /**
     * Constructor.
     *
     * @param Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $reflector = new ReflectionFunction($closure);

        if (preg_match_all('/\s*\*\s+([^@*]+)\s*/mus', $reflector->getDocComment(), $matches)) {
            $this->description = trim(implode(PHP_EOL, array_map('trim', $matches[1])));
        }

        $this->name = $reflector->getName();
        $parameters = array_map(function ($parameter) {
            return '$' . $parameter->getName();
        }, array_slice($reflector->getParameters(), 1));

        $this->paramlist = implode(', ', $parameters);
    }

    /**
     * Add an alias to this check.
     *
     * @param string $alias
     *
     * @return int the number of aliases registered
     */
    public function addAlias($alias)
    {
        $this->aliases[] = $alias;

        return count($this->aliases);
    }

    /**
     * Get the descitpion of this check.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Get the aliases of this check.
     *
     * @return string[]
     */
    public function aliases()
    {
        return $this->aliases;
    }
}