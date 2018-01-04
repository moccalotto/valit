<?php

namespace Valit\Logic;

use Valit\Result\AssertionResult;

class AllOrNone extends BaseLogic
{
    /**
     * Internal.
     *
     * @return AssertionResult
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

        $res = ($successCount === $scenarioCount)
            || ($successCount === 0);

        return new AssertionResult(
            $res,
            'All or none of the {scenarioResults:count} given scenarios must succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
