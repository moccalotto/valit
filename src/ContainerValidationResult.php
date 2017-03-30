<?php

namespace Moccalotto\Valit;

class ContainerValidationResult
{
    /**
     * @var array
     */
    protected $results;

    /**
     * Constructor.
     *
     * @param array $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function results()
    {
        return $this->results;
    }

    public function errors()
    {
        return array_map(function ($fluent) {
            return $fluent->errorMessages();
        }, $this->results);
    }

    public function renderedResults()
    {
        return array_map(function ($fluent) {
            return $fluent->renderedResults();
        }, $this->results);
    }
}
