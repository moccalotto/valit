<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class AnyOf extends BaseLogic
{
    /**
     * Internal.
     *
     * @return AssertionResult
     */
    public function makeResult()
    {
        $scenarioResults = [];
        $successCount = 0;
        foreach ($this->executor->results() as $result) {
            $scenarioResults[] = $result->results();
            $successCount += (int) $result->success();
        }

        return new AssertionResult(
            $successCount > 0,
            'At least one of {scenarioResults:count} scenarios must succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
