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

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->args)) {
            return $this->name;
        }

        return sprintf(
            '%s(%s)',
            $this->name,
            json_encode($this->args, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)
        );
    }
}
