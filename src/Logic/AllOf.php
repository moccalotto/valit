<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class AllOf extends BaseLogic
{
    /**
     * @return AssertionResult
     *
     * @internal
     */
    public function makeResult()
    {
        $scenarioResults = [];
        $scenarioCount = 0;
        $successCount = 0;
        foreach ($this->executor->results() as $result) {
            $scenarioResults[] = $result->results();
            $successCount += (int) $result->success();
            ++$scenarioCount;
        }

        return new AssertionResult(
            $successCount === $scenarioCount,
            'All of the given scenarios must succeed. Only {successCount} of {scenarioResults:count} succeeded',
            compact('successCount', 'scenarioResults')
        );
    }
}
