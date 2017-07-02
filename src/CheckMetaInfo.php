<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit;

use Closure;
use JsonSerializable;
use ReflectionFunction;

class CheckMetaInfo implements JsonSerializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string[]
     */
    protected $aliases;

    /**
     * @var string
     */
    protected $paramlist;

    /**
     * Constructor
     *
     * @param Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $reflector = new ReflectionFunction($closure);

        if (preg_match('/^\s*\*\s*(.+?)\s*$/m', $reflector->getDocComment(), $matches)) {
            $this->description = $matches[1];
        }

        $this->name = $reflector->getName();
        $parameters = array_map(function ($parameter) {
            return '$' . $parameter->getName();
        }, array_slice($reflector->getParameters(), 1));

        $this->paramlist = implode(', ', $parameters);
    }

    /**
     * Serialize to json.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'aliases' => $this->aliases,
            'paramlist' => $this->paramlist,
        ];
    }

    /**
     * Helper for var_dump, print_r, and company.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->jsonSerialize();
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
