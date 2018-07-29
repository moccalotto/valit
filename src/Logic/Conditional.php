<?php

namespace Valit\Logic;

use Valit\Manager;
use Valit\Result\AssertionResult;

class Conditional extends BaseLogic
{
    /**
     * Constructor.
     *
     * @param Manager $manager
     * @param mixed   $condition
     * @param mixed   $then
     * @param mixed   $else
     */
    public function __construct(Manager $manager, $condition, $then, $else)
    {
        parent::__construct(
            $manager,
            [$condition, $then, $else]
        );
    }

    /**
     * Internal.
     *
     * @return AssertionResult
     */
    public function makeResult()
    {
        list($condition, $then, $else) = $this->executor->results();

        $success = $condition->success()
            ? $then->success()
            : $else->success();

        return new AssertionResult(
            $success,
            'At least one path in the if-then-else conditional must succeed',
            compact('condition', 'then', 'else')
        );
    }
}
