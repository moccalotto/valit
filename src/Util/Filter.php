<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Util;

use LogicException;

/**
 * A Container for filters
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
     * Constructor
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
