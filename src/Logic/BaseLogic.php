<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use Valit\Contracts\Logic;
use Valit\Result\AssertionResult;

abstract class BaseLogic implements Logic
{
    /**
     * @var Executor
     *
     * @internal
     */
    public $executor;

    /**
     * Constructor.
     *
     * @param Manager           $manager
     * @param Traversable|array $scenarios
     */
    public function __construct(Manager $manager, $scenarios)
    {
        $this->executor = new Executor($manager, $scenarios);
    }

    /**
     * The requirements of this logic.
     *
     * It can be 'none', 'simple value' or 'container'
     *
     * @return string
     */
    public function requirements()
    {
        return $this->executor->requires;
    }

    /**
     * Execute the logic.
     *
     * @param bool  $withValue
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function execute($hasValue = false, $value = null)
    {
        $this->executor->execute($hasValue, $value);

        return $this->makeResult();
    }

    /**
     * Execute the logic with a given value.
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function withValue($value)
    {
        return $this->execute(true, $value);
    }

    /**
     * Alias of withValue.
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function whereValueIs($value)
    {
        return $this->withValue($value);
    }

    /**
     * Execute the logic without a value.
     *
     * @return AssertionResult
     */
    public function withoutValue()
    {
        return $this->execute(false);
    }

    /**
     * @return AssertionResult
     */
    abstract protected function makeResult();
}
