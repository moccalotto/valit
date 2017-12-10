<?php

namespace Valit\Logic;

use Valit\Manager;
use LogicException;
use Valit\Result\AssertionResultBag;
use Valit\Validators\ValueValidator;
use Valit\Result\ContainerResultBag;
use Valit\Validators\ContainerValidator;

class OneOf
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
     * @var mixed
     *
     * @internal
     */
    public $checks;

    /**
     * @var string
     *
     * @internal
     */
    public $requires;

    /**
     * @var ContainerResultBag
     *
     * @internal
     */
    public $results;

    /**
     * Constructor.
     *
     * @param Manager $manager
     * @param mixed   $checks
     */
    public function __construct(Manager $manager, $checks)
    {
        $this->results = new ContainerResultBag([], 'OneOf-logic');
        $this->checks = $checks;
        $this->manager = $manager;
        $this->hasValue = false;
        $this->requires = static::REQUIRES_NONE;
    }

    public function require($newRequirement)
    {
        if ($newRequirement === static::REQUIRES_NONE) {
            return $this->requires;
        }

        if ($newRequirement === $this->requires) {
            return $this->requires;
        }

        if ($this->requires === static::REQUIRES_NONE) {
            return $newRequirement;
        }

        throw new LogicException(sprintf(
            'Cannot add a set of checks that requires a %s because a previously added set required a %s',
            $newRequirement,
            $this->requires
        ));
    }

    /**
     * Execute the logic.
     *
     * @param mixed $value (optional) The value or container to be used in the logic (if any)
     */
    public function execute()
    {
        $this->hasValue = func_num_args() >= 1;
        $this->value = $this->hasValue ? func_get_arg(0) : null;
        $this->requires = statc::REQUIRES_NONE;

        $scenarioNo = 0;
        $scenarios = [];

        foreach ($this->checks as $key => $value) {
            ++$scenarioNo;

            if (is_string($key)) {
                $scenarios[$scenarioNo] = $this->executeContainerValidation($key, $value);
            } elseif (is_a($value, Template::class)) {
                $scenarios[$scenarioNo] = $this->executeTemplate($value);
            } elseif (is_a($value, AssertionResultBag::class)) {
                $scenarios[$scenarioNo] = $this->addAssertionResultBag($value);
            } elseif (is_string($value)) {
                $scenarios[$scenarioNo] = $this->executeString($value);
            } else {
                throw new WhatTheFuck('!');
            }
        }

        return $this->makeResult($scenarios);
    }

    protected function makeResult($scenarios)
    {
        $successCount = 0;
        foreach ($scenarios as $scenario) {
            $successCount += $scenario->success();
        }

        return new AssertionResult(
            $successCount === 1,
            'Exactly one scenario must succeed, but {successCount:int} succeeded',
            compact('successCount', 'scenarios')
        );
    }

    protected function addAssertionResultBag($validator)
    {
        return new ContainerResultBag([$validator]);
    }

    protected function executeString($assertions)
    {
        $normalizedAssertions = AssertionNormalizer::normalize($assertions);

        $template = Template::fromAssertionBag($normalizedAssertions);

        return $this->executeTemplate($template);
    }

    protected function executeTemplate($template)
    {
        $resultBag = $template->whereValueIs(
            $this->value,
            null,
            $this->manager
        );

        return $this->addAssertionResultBag($resultBag);
    }

    protected function executeContainerValidation($fieldNameGlob, $assertions)
    {
        $this->require(static::REQUIRES_CONTAINER);

        $validator = new ContainerValidator($this->manager, $this->value, false);

        $resultBag = $validator->passes([
            $fieldNameGlob => $assertions,
        ]);

        return $resultBag;
    }
}
