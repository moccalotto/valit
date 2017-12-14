<?php

namespace Valit\Assertion;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use BadMethodCallException;

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
     * Is the flag with the given name raised?
     *
     * @param string $key
     *
     * @return bool
     */
    public function is($key)
    {
        return isset($this->flags[$key]);
    }

    /**
     * Alias of calling is().
     *
     * @param string $key
     *
     * @return bool|null
     */
    public function flag($key)
    {
        return $this->is($key);
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

    /**
     * Are the assertions in this bag optional?
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->is('optional');
    }

    /**
     * Magic method to support the is* method calls.
     *
     * Calling isFoo() is the same as calling is('foo')
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($methodName, $args)
    {
        if (strpos($methodName, 'is') === 0) {
            return $this->is(lcfirst(substr($methodName, 2)));
        }

        throw new BadMethodCallException(sprintf(
            'The method »%s« does not exist',
            $methodName
        ));
    }
}
