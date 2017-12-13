<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use LogicException;
use Valit\Template;
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

    protected function require($newRequirement)
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
                if (!(is_array($this->value) || $this->value instanceof Traversable)) {
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
            } elseif (is_a($value, Template::class)) {
                $this->results[] = $this->executeTemplate($value);
            } elseif (is_a($value, AssertionResultBag::class)) {
                $this->results[] = $this->addAssertionResultBag($value);
            } elseif (is_a($value, AssertionResult::class)) {
                $this->results[] = $this->addAssertionResult($value);
            } elseif (is_a($value, LogicContract::class)) {
                $this->results[] = $this->executeLogic($value);
            } elseif (is_string($value)) {
                $this->results[] = $this->executeString($value);
            } else {
                throw new LogicException('This Should Never Happen: ' . gettype($key) . ' => ' . gettype($value));
            }
        }

        return $this->results;
    }

    protected function executeLogic(LogicContract $logic)
    {
        $this->require($logic->requirements());

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
            'value',
            false
        );

        $resultBag->addAssertionResult($asserionResult);

        return $this->addAssertionResultBag($resultBag);
    }

    /**
     * @param string $assertions
     *
     * @return ContainerResultBag
     */
    protected function executeString($assertions)
    {
        $this->require(static::REQUIRES_VALUE);

        $normalizedAssertions = AssertionNormalizer::normalize($assertions);

        $template = Template::fromAssertionBag($normalizedAssertions);

        return $this->executeTemplate($template);
    }

    /**
     * @param Template $template
     *
     * @return ContainerResultBag
     */
    protected function executeTemplate(Template $template)
    {
        $this->require(static::REQUIRES_VALUE);

        $resultBag = $template->whereValueIs(
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
        $this->require(static::REQUIRES_CONTAINER);

        $validator = new ContainerValidator($this->manager, $this->value, false);

        $resultBag = $validator->passes([
            $fieldNameGlob => $assertions,
        ])->alias('value');

        return $resultBag;
    }
}
