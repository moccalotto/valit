<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Container\Filter;
use BadMethodCallException;
use Valit\Contracts\FluentCheckInterface;

class Template
{
    /**
     * @var array
     *
     * @internal
     */
    public $checks = [];

    /**
     * Constructor.
     *
     * @param bool $throwOnFailure Should we throw an exception as soon as we encounter a failed check
     */
    public function __construct($throwOnFailure = false)
    {
        $this->throwOnFailure = (bool) $throwOnFailure;
    }

    /**
     * Execute checks by "calling" them.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return $this
     */
    public function __call($methodName, $args)
    {
        if ($methodName === 'as') {
            throw new BadMethodCallException(sprintf(
                'You cannot set the variable alias on a template',
                $methodName
            ));
        }

        return $this->addCheck($methodName, $args);
    }

    /**
     * Add a check to the template.
     *
     * @param string $name
     * @param array  $args
     *
     * @return $this
     */
    public function addCheck($name, array $args)
    {
        $this->checks[] = new Filter($name, $args);

        return $this;
    }

    /**
     * Apply all the stored checks to a Fluent instance.
     *
     * @return Fluent
     */
    public function executeOnFluent(FluentCheckInterface $fluent)
    {
        foreach ($this->checks as $check) {
            $fluent->executeCheck(
                $check->name,
                $check->args
            );
        }

        return $fluent;
    }

    /**
     * Create a new Fluent, apply all stored checks on it, and return it.
     *
     * @param mixed             $value   The value to be checked
     * @param string|null       $varName The alias/name of the value
     * @param CheckManager|null $manager The check manager to use.
     *                                   If none given, the default
     *                                   manager will be used
     *
     * @return Fluent
     */
    public function whereValueIs($value, $varName = null, CheckManager $manager = null)
    {
        if ($manager === null) {
            $manager = Manager::instance();
        }

        $fluent = new FLuent($manager, $value, $this->throwOnFailure);

        if ($varName) {
            $fluent->alias((string) $this->varName);
        }

        return $this->executeOnFluent($fluent);
    }
}
