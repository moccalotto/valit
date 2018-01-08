<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use Valit\Util\Val;
use Valit\Contracts\Logic;
use Valit\Contracts\Result;
use Valit\Result\AssertionResult;

abstract class BaseLogic implements Logic, Result
{
    /**
     * Internal.
     *
     * @var Executor
     */
    public $executor;

    /**
     * Intenral: The result generated when execute() was called last.
     *
     * @var AssertionResult|null
     */
    public $cachedResult = null;

    /**
     * Internal: The $hasValue when execute() was called last.
     *
     * @var bool
     */
    public $hasValue;

    /**
     * The $value when execute() was called last.
     *
     * Internal.
     *
     * @var mixed
     */
    public $value;

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
    public function requires()
    {
        return $this->executor->requires;
    }

    /**
     * Execute the logic.
     *
     * @param bool  $hasValue
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function execute($hasValue = false, $value = null)
    {
        if (!$hasValue) {
            $value = null;
        }

        // Clear cached result if this method is called with
        // other args than last time it was called.
        if ($hasValue !== $this->hasValue || $value !== $this->value) {
            $this->cachedResult = null;
        }

        if (!$this->cachedResult) {
            $this->executor->execute($hasValue, $value);

            $this->cachedResult = $this->makeResult();
        }

        return $this->cachedResult;
    }

    /**
     * Did the validations succeed?
     *
     * @return bool
     */
    public function success()
    {
        if ($this->cachedResult) {
            return $this->cachedResult->success();
        }

        return $this->execute()->success();
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
     * Clear debug info.
     *
     * @return array
     */
    public function __debugInfo()
    {
        $executed = $this->cachedResult !== null;

        return [
            'scenarios' => Val::count($this->executor->scenarios),
            'executed' => $executed,
            'hasValue' => $executed ? ($this->hasValue) : null,
            'valueType' => $this->value,
        ];
    }

    /**
     * Internal: create the result.
     *
     * @return AssertionResult
     */
    abstract public function makeResult();
}
