<?php

namespace Valit\Logic;

use Traversable;
use Valit\Manager;
use Valit\Template;
use Valit\Contracts\Logic;
use Valit\Result\AssertionResult;
use Valit\Result\AssertionResultBag;
use Valit\Result\ContainerResultBag;
use Valit\Assertion\AssertionNormalizer;
use Valit\Validators\ContainerValidator;
use Valit\Exceptions\ValueRequiredException;
use Valit\Exceptions\ContainerRequiredException;

class OneOf implements Logic
{
    const REQUIRES_NONE = 'none';
    const REQUIRES_VALUE = 'simple value';
    const REQUIRES_CONTAINER = 'container';

    /**
     * @var Executor
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
            'Exactly one out of {scenarioResults:count} scenarios must succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
