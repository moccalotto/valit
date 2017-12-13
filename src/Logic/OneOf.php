<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class OneOf extends BaseLogic
{
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
