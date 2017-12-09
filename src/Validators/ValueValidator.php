<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Validators;

use BadMethodCallException;
use Valit\Contracts\CheckManager;
use Valit\Traits\ContainsResults;

class ValueValidator
{
    use ContainsResults;

    /**
     * @var CheckManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param CheckManager $manager        The manager that contains all our checks
     * @param mixed        $value          The value we are validating
     * @param bool         $throwOnFailure Should we throw an exception as soon as we encounter a failed result
     */
    public function __construct(CheckManager $manager, $value, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->value = $value;
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
            return $this->alias($args[0]);
        }

        if (!$this->manager->hasCheck($methodName)) {
            throw new BadMethodCallException(sprintf(
                'Unknown method name "%s"',
                $methodName
            ));
        }

        return $this->executeCheck($methodName, $args);
    }

    /**
     * Execute a check.
     *
     * @param string $checkName
     * @param array  $args
     *
     * @return $this
     */
    public function executeCheck($checkName, array $args)
    {
        $result = $this->manager->executeCheck($checkName, $this->value, $args);

        if (is_array($result)) {
            $this->registerManyResults($result);
        } else {
            $this->registerResult($result);
        }

        return $this;
    }
}
