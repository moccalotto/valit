<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Container;

/**
 * A single filter (i.e. a single check).
 */
class Filter
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $args;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $args
     */
    public function __construct($name, $args)
    {
        $this->name = $name;
        $this->args = $args;
    }
}