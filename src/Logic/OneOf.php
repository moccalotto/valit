<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use Valit\Template;
use Valit\Result\AssertionResult;
use Valit\Result\AssertionResultBag;
use Valit\Result\ContainerResultBag;
use Valit\Assertion\AssertionNormalizer;
use Valit\Validators\ContainerValidator;
use Valit\Exceptions\ValueRequiredException;
use Valit\Exceptions\ContainerRequiredException;

class OneOf
{
    const REQUIRES_NONE = 'none';
    const REQUIRES_VALUE = 'simple value';
    const REQUIRES_CONTAINER = 'container';

    /**
     * @var Executer
     *
     * @internal
     */
    public $executor;

    /**
     * @var string
     */
    public $requires;


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
     * Get the executed scenarios.
     *
     * @return ContainerResultBag[]
     */
    public function scenarios()
    {
        return $this->scenarios;
    }

    protected function require($newRequirement)
    {
        if ($newRequirement === static::REQUIRES_NONE) {
            return;
        }

        if ($newRequirement === $this->requires) {
            return;
        }

        switch ($newRequirement) {
            case $this->requires:
            case static::REQUIRES_NONE:
                return;
            case static::REQUIRES_VALUE:
                if (!$this->hasValue) {
                    throw new ValueRequiredException(
                        'Cannot add a branch that requires a value. No value provided'
                    );
                }
                $this->requires = $newRequirement;

                return;
            case static::REQUIRES_CONTAINER:
                if (!$this->hasValue) {
                    throw new ValueRequiredException(
                        'Cannot add a branch that requires a container. No value provided'
                    );
                }
                if (!(is_array($this->value) || $this->value instanceof Traversable)) {
                    throw new ContainerRequiredException(
                        'Cannot add a branch that requires a container. The value provided is not a container'
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
     * @return AssertionResult
     */
    public function execute($hasValue = false, $value = null)
    {
        $this->executor->execute($hasValue, $value);

        return $this->makeResult();
    }

    /**
     * Execute the logic with a given value
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
    protected function makeResult()
    {
        $scenarioResults = [];
        $scenarioCount = 0;
        $successCount = 0;
        foreach ($this->executor->results() as $result) {
            $scenarioResults[] = $result->renderedResults();
            $successCount += (int) $result->success();
            ++$scenarioCount;
        }

        return new AssertionResult(
            $successCount === 1,
            'Exactly one out of {scenarioResults:count} scenarios must succeed, but {successCount:int} succeeded.',
            compact('successCount', 'scenarioResults')
        );
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
