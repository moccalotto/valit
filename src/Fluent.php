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
use Valit\Contracts\CheckManager;
use Valit\Contracts\FluentCheckInterface;

class Fluent implements FluentCheckInterface
{
    use Traits\ContainsResults;

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
    final public function __construct(CheckManager $manager, $value, $throwOnFailure)
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

        $result = $this->manager->executeCheck($methodName, $this->value, $args);

        if (is_array($result)) {
            $this->registerManyResults($result);
        } else {
            $this->registerResult($result);
        }

        return $this;
    }
}
