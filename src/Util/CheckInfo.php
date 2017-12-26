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

        if (preg_match_all('/\s*\*\s?([^@*]+)\s*/mus', $reflector->getDocComment(), $matches)) {
            $this->parseDocs($matches[1]);
        }

        $this->name = $reflector->getName();
        $parameters = array_map(function ($parameter) {
            return '$'.$parameter->getName();
        }, array_slice($reflector->getParameters(), 1));

        $this->paramlist = implode(', ', $parameters);
    }

    /**
     * Parse doc block to extract a headline and a description.
     *
     * @param string[] $docs
     */
    protected function parseDocs($docs)
    {
        $lastLineEmpty = null;
        $started = false;
        $lines = [];
        foreach ($docs as $line) {
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
