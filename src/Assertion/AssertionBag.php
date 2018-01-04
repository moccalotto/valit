<?php

namespace Valit\Assertion;

use Countable;
use ArrayIterator;
use Valit\Manager;
use IteratorAggregate;
use Valit\Validators\ValueValidator;

class AssertionBag implements IteratorAggregate, Countable
{
    /**
     * Internal.
     *
     * @var Assertion[]
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
    public function addAssertion(Assertion $assertion)
    {
        $this->assertions[] = $assertion;

        return $this;
    }

    /**
     * Create an assertion and add it.
     *
     * @param string $name
     * @param array  $args
     *
     * @return $this
     */
    public function addNewAssertion($name, $args)
    {
        if (in_array($name, ['required', 'isRequired', 'present', 'isPresent'])) {
            return $this->setRequired();
        }

        if (in_array($name, ['optional', 'isOptional'])) {
            return $this->setOptional();
        }

        return $this->addAssertion(new Assertion($name, $args));
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

    /**
     * Apply all the stored assertions to a ValueValidator instance.
     *
     * @return ValueValidator
     */
    public function applyToValidator(ValueValidator $validator)
    {
        foreach ($this->assertions as $assertion) {
            $validator->executeCheck(
                $assertion->name,
                $assertion->args
            );
        }

        return $validator;
    }

    /**
     * Execute contained assertions in "check"-mode.
     * I.e. do not throw an InvalidValueException if an assertion fails.
     *
     * Create a new ValueValidator, apply all stored assertions on it, and return it.
     *
     * @param mixed        $value   The value to be checked
     * @param string|null  $varName The alias/name of the value
     * @param Manager|null $manager The check manager to use.
     *                              If none given, the default
     *                              manager will be used
     *
     * @return ValueValidator a validator that will not throw exceptions on failures
     */
    public function whereValueIs($value, $varName = null, Manager $manager = null)
    {
        if ($manager === null) {
            $manager = Manager::instance();
        }

        $validator = new ValueValidator($manager, $value, false);

        if ($varName) {
            $validator->alias((string) $varName);
        }

        return $this->applyToValidator($validator);
    }

    /**
     * Set the »optional« flag.
     *
     * @return $this
     */
    public function setOptional()
    {
        return $this->setFlag('optional', true);
    }

    /**
     * Unset the »optional« flag.
     *
     * @return $this
     */
    public function setRequired()
    {
        return $this->setFlag('optional', false);
    }

    /**
     * Add assertions by "calling" them.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return $this
     */
    public function __call($methodName, $args)
    {
        return $this->addNewAssertion($methodName, $args);
    }
}
