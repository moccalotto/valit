<?php

namespace Valit\Logic;

use Valit\Manager;
use Valit\Result\AssertionResult;

class Not extends BaseLogic
{
    /**
     * Constructor.
     *
     * @param Manager  $manager
     * @param mixed    $scenario
     */
    public function __construct(Manager $manager, $scenario)
    {
        parent::__construct($manager, [$scenario]);
    }

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
            'The scenario may not succeed',
            compact('successCount', 'scenarioResults')
        );
    }
}
