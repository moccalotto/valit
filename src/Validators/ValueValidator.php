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
use Valit\Result\AssertionResultBag;
use Valit\Contracts\CheckManager;

class ValueValidator extends AssertionResultBag
{
    /**
     * @var CheckManager
     */
    protected $manager;

    /**
     * @var AssertionResultBag
     */
    protected $results;

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
        parent::__construct($value, 'value', (bool) $throwOnFailure);
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
            $this->alias($args[0]);

            return $this;
        }

        if (!$this->manager->hasCheck($methodName)) {
            throw new BadMethodCallException(sprintf(
                'Unknown method name "%s"',
                $methodName
            ));
        }

        $this->executeCheck($methodName, $args);

        return $this;
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

        $this->addAssertionResult($result);

        return $this;
    }
}
