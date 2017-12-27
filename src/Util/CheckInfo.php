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
    public $headline;

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

        $docblock = $reflector->getDocComment();

        if (preg_match_all('/\s*\*\s?([^@*]+)\s*/mus', $docblock, $matches)) {
            $this->parseDescription($matches[1]);
        }

        $this->name = $reflector->getName();
        $parameters = array_map(function ($param) use ($docblock) {
            return $this->makeParamString($param, $docblock);
        }, array_slice($reflector->getParameters(), 1));

        $this->paramlist = implode(', ', $parameters);
    }

    /**
     * Build a param string used to generate a method signature.
     *
     *
     * Example outputs:
     *
     * int $foo
     * mixed $bar
     * [SimpleXmlElement $xml = null]
     *
     *
     * @param \ReflectionParameter $param
     * @param string               $docblock
     *
     * @return string
     */
    protected function makeParamString($param, $docblock)
    {
        $name = $param->getName();
        $type = $this->inferType($param, $docblock);

        if (!$param->isOptional()) {
            return sprintf('%s $%s', $type, $name);
        }

        $defaultValue = $param->isDefaultValueConstant()
            ? $param->getDefaultValueConstantName()
            : json_encode($param->getDefaultValue());

        return sprintf('[%s $%s = %s]', $type, $name, $defaultValue);
    }

    protected function inferType($param, $docblock)
    {
        // use php7 type if available
        if (method_exists($param, 'hasType') && $param->hasType()) {
            return $param->getType();
        }

        // If the parameter is class-hinted, return the hinted class.
        if ($param->getClass()) {
            return $param->getClass();
        }

        // If the parameter is defined as a callable, use that hint
        if ($param->isCallable()) {
            return 'callable';
        }

        // Use docblock if available
        $regex = sprintf('/@param\s+(\S+)\s+\$%s\b/', preg_quote($param->getName()));
        if (preg_match($regex, $docblock, $matches)) {
            return ltrim($matches[1], '\\');
        }

        // If paramter is type-hinted as an array, use that hint.
        if ($param->isArray()) {
            return 'array';
        }

        return 'mixed';
    }

    /**
     * Parse doc block to extract a headline and a description.
     *
     * @param string[] $docblock
     */
    protected function parseDescription($docblock)
    {
        $lastLineEmpty = null;
        $started = false;
        $lines = [];
        foreach ($docblock as $line) {
            $line = rtrim($line);
            $trimmed = trim($line);
            $currentLineEmpty = $trimmed === '';
            $started = $started || !$currentLineEmpty;

            // Skip initial empty lines
            if ($currentLineEmpty && empty($lines)) {
                continue;
            }

            // If we hit two consecutive empty lines in a row, we're done
            if ($currentLineEmpty && $lastLineEmpty) {
                break;
            }

            // We stop if we encounter an annotation
            if (substr($trimmed, 0, 1) === '@') {
                break;
            }

            $lines[] = $line;

            $lastLineEmpty = $currentLineEmpty;
        }

        $text = implode(PHP_EOL, $lines);

        list($this->headline, $this->description) = array_map('trim', explode('.', $text, 2));
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
     * Get the headline for this check.
     *
     * @return string
     */
    public function headline()
    {
        return $this->headline;
    }

    /**
     * Get the description of this check.
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
