<?php

namespace Valit\Assertion;

/**
 * A single assertion.
 *
 * An assertion is the execution of a single check.
 */
class Assertion
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
