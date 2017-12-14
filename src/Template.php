<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use BadMethodCallException;
use Valit\Assertion\Assertion;
use Valit\Contracts\CheckManager;
use Valit\Assertion\AssertionBag;
use Valit\Validators\ValueValidator;

class Template
{
    /**
     * @var AssertionBag
     *
     * @internal
     */
    public $assertions;

    /**
     * @var bool
     */
    public $throwOnFailure;

    /**
     * Constructor.
     *
     * @param bool $throwOnFailure Should we throw an exception as soon as we encounter a failed check
     */
    public function __construct()
    {
        $this->assertions = new AssertionBag();
    }

    /**
     * Factory.
     *
     * @param AssertionBag $assertions
     *
     * @return Template
     */
    public static function fromAssertionBag(AssertionBag $assertions)
    {
        $instance = new static();

        $instance->assertions = $assertions;

        return $instance;
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
        if ($methodName === 'as') {
            throw new BadMethodCallException('You cannot set the variable alias on a template');
        }

        return $this->addAssertion($methodName, $args);
    }

    /**
     * Add an assertion to the template.
     *
     * @param string $name
     * @param array  $args
     *
     * @return $this
     */
    public function addAssertion($name, array $args)
    {
        $this->assertions->add(
            new Assertion($name, $args)
        );

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
     * Execute this template in "check"-mode.
     *
     * Create a new ValueValidator, apply all stored assertions on it, and return it.
     *
     * @param mixed             $value   The value to be checked
     * @param string|null       $varName The alias/name of the value
     * @param CheckManager|null $manager The check manager to use.
     *                                   If none given, the default
     *                                   manager will be used
     *
     * @return ValueValidator a validator that will not throw exceptions on failures
     */
    public function whereValueIs($value, $varName = null, CheckManager $manager = null)
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
}
