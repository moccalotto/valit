<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class NoneOf extends BaseLogic
{
    /**
     * @return AssertionResult
     */
    protected function makeResult()
    {
        $scenarioResults = [];
        $successCount = 0;
        foreach ($this->executor->results() as $result) {
            $scenarioResults[] = $result->renderedResults();
            $successCount += (int) $result->success();
        }

        return new AssertionResult(
            $successCount === 0,
            'At none of {scenarioResults:count} scenarios may succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
