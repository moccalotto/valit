<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use LogicException;
use Valit\Util\Val;
use Valit\Assertion\AssertionBag;
use Valit\Result\AssertionResult;
use Valit\Result\AssertionResultBag;
use Valit\Result\ContainerResultBag;
use Valit\Assertion\AssertionNormalizer;
use Valit\Validators\ContainerValidator;
use Valit\Contracts\Logic as LogicContract;
use Valit\Exceptions\ValueRequiredException;
use Valit\Exceptions\ContainerRequiredException;

class Executor
{
    const REQUIRES_NONE = 'none';
    const REQUIRES_VALUE = 'simple value';
    const REQUIRES_CONTAINER = 'container';

    /**
     * @var Manager
     *
     * @internal
     */
    public $manager;

    /**
     * @var bool
     */
    public $hasValue;

    /**
     * @var mixed
     */
    public $value;

    /**
     * The logic paths.
     *
     * @param Traversable|array $chceks
     *
     * @internal
     */
    public $scenarios;

    /**
     * @var string
     *
     * @internal
     */
    public $requires;

    /**
     * @var ContainerResultBag[]
     *
     * @internal
     */
    public $results;

    /**
     * Constructor.
     *
     * @param Manager           $manager
     * @param Traversable|array $scenarios
     */
    public function __construct(Manager $manager, $scenarios)
    {
        $this->scenarios = $scenarios;
        $this->manager = $manager;
        $this->hasValue = false;
        $this->requires = static::REQUIRES_NONE;
        $this->results = [];
    }

    /**
     * Get the executed results.
     *
     * @return ContainerResultBag[]
     */
    public function results()
    {
        return $this->results;
    }

    protected function requires($newRequirement)
    {
        if ($newRequirement === static::REQUIRES_NONE) {
            return $this->requires;
        }

        if ($newRequirement === $this->requires) {
            return $this->requires;
        }

        switch ($newRequirement) {
            case $this->requires:
            case static::REQUIRES_NONE:
                return;
            case static::REQUIRES_VALUE:
                if (!$this->hasValue) {
                    throw new ValueRequiredException(
                        'Cannot add a scenario that requires a value. No value provided'
                    );
                }
                $this->requires = $newRequirement;

                return;
            case static::REQUIRES_CONTAINER:
                if (!$this->hasValue) {
                    throw new ValueRequiredException(
                        'Cannot add a scenario that requires a container. No value provided'
                    );
                }
                if (!Val::canTraverse($this->value)) {
                    throw new ContainerRequiredException(
                        'Cannot add a scenario that requires a container. The value provided is not a container'
                    );
                }
                $this->requires = $newRequirement;

                return;
        }
    }

    /**
     * Execute the logic.
     *
     * @param bool  $withValue
     * @param mixed $value
     *
     * @return AssertionResultBag[]
     */
    public function execute($hasValue = false, $value = null)
    {
        $this->hasValue = (bool) $hasValue;
        $this->value = $value;
        $this->requires = static::REQUIRES_NONE;
        $this->results = [];

        foreach ($this->scenarios as $key => $value) {
            if (is_string($key)) {
                $this->results[] = $this->executeContainerValidation($key, $value);
            } elseif (is_a($value, AssertionResultBag::class)) {
                $this->results[] = $this->addAssertionResultBag($value);
            } elseif (is_a($value, AssertionResult::class)) {
                $this->results[] = $this->addAssertionResult($value);
            } elseif (is_a($value, LogicContract::class)) {
                $this->results[] = $this->executeLogic($value);
            } elseif (is_a($value, AssertionBag::class)) {
                $this->results[] = $this->executeAssertions($value);
            } elseif (is_string($value)) {
                $this->results[] = $this->executeAssertions($value);
            } elseif (is_array($value)) {
                $this->results[] = $this->executeAssertions($value);
            } else {
                throw new LogicException(sprintf(
                    'Unknown check type: %s => %s',
                    Val::escape($key),
                    Val::escape($value)
                ));
            }
        }

        return $this->results;
    }

    protected function executeLogic(LogicContract $logic)
    {
        $this->requires($logic->requirements());

        $asserionResult = $logic->execute($this->hasValue, $this->value);

        return $this->addAssertionResult($asserionResult);
    }

    /**
     * @param AssertionResultBag $resultBag
     *
     * @return ContainerResultBag
     */
    protected function addAssertionResultBag(AssertionResultBag $resultBag)
    {
        return new ContainerResultBag([$resultBag], 'value');
    }

    /**
     * @param AssertionResult $asserionResult
     *
     * @return ContainerResultBag
     */
    protected function addAssertionResult(AssertionResult $asserionResult)
    {
        $resultBag = new AssertionResultBag(
            $this->value,
            'value'
        );

        $resultBag->addAssertionResult($asserionResult);

        return $this->addAssertionResultBag($resultBag);
    }

    /**
     * Execute a number of assertions on a value
     * and return a ContainerResultBag.
     *
     * @param string|array|AssertionBag $assertions
     *
     * @return ContainerResultBag
     */
    protected function executeAssertions($assertions)
    {
        $this->requires(static::REQUIRES_VALUE);

        $normalizedAssertions = AssertionNormalizer::normalize($assertions);

        $resultBag = $normalizedAssertions->whereValueIs(
            $this->value,
            null,
            $this->manager
        );

        return $this->addAssertionResultBag($resultBag);
    }

    /**
     * @param string            $fieldNameGlob
     * @param array|Traversable $container
     *
     * @return ContainerResultBag
     */
    protected function executeContainerValidation($fieldNameGlob, $assertions)
    {
        $this->requires(static::REQUIRES_CONTAINER);

        $validator = new ContainerValidator($this->manager, $this->value, false);

        $resultBag = $validator->passes([
            $fieldNameGlob => $assertions,
        ])->alias('value');

        return $resultBag;
    }

    /**
     * Debug info.
     *
     * This method reduces the size of print_r and var_dump()
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'requires' => $this->requires,
            'hasValue' => $this->hasValue,
            'value' => $this->value,
            'scenarios' => $this->scenarios,
            'results' => $this->results,
        ];
    }
}
