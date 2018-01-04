<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class OneOf extends BaseLogic
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
            $successCount === 1,
            'Exactly one out of {scenarioResults:count} scenarios must succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
