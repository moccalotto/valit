<?php

namespace Valit\Assertion;

use Countable;
use ArrayIterator;
use IteratorAggregate;

class AssertionBag implements IteratorAggregate, Countable
{
    /**
     * @var Assertion[]
     *
     * @internal
     */
    public $assertions;

    /**
     * @var array
     */
    public $flags = [];

    /**
     * Constructor.
     *
     * @param Assertion[] $assertions
     */
    public function __construct($assertions = [])
    {
        $this->assertions = $assertions;
    }

    /**
     * Add an assertion.
     *
     * @param Assertion $assertion
     *
     * @return $this
     */
    public function add(Assertion $assertion)
    {
        $this->assertions[] = $assertion;

        return $this;
    }

    /**
     * Get all the assertions.
     *
     * @return Assertion[]
     */
    public function all()
    {
        return $this->assertions;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->assertions);
    }

    /**
     * The number of assertions in the bag.
     *
     * @return int
     */
    public function count()
    {
        return count($this->assertions);
    }

    /**
     * Alias of calling is().
     *
     * @param string $key
     *
     * @return bool|null
     */
    public function hasFlag($key)
    {
        return isset($this->flags[$key]);
    }

    /**
     * Set the flag with the given name.
     *
     * @param string $key
     * @param bool   $yes
     *
     * @return $this
     */
    public function setFlag($key, $yes = true)
    {
        if ($yes) {
            $this->flags[$key] = true;
        } else {
            unset($this->flags[$key]);
        }

        return $this;
    }
}
