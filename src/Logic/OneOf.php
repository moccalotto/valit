<?php

namespace Valit\Logic;

use Valit\Manager;
use LogicException;
use Valit\Contracts\CheckManager;
use Valit\Validators\ValueValidator;

class OneOf
{
    const REQUIRES_NONE = 'none';
    const REQUIRES_VALUE = 'simple value';
    const REQUIRES_CONTAINER = 'container';

    /**
     * @var string
     */
    protected $requires;

    /**
     * Constructor.
     *
     * @param array $checks
     */
    public function __construct($checks)
    {
        $this->requires = static::REQUIRES_NONE;
    }

    public function addRequirement($requires)
    {
        if ($requires === static::REQUIRES_NONE) {
            return;
        }

        if ($requires === $this->requires) {
            return;
        }

        if ($this->requires !== static::REQUIRES_NONE) {
            throw new LogicException(sprintf(
                'Cannot add a set of checks that requires a %s because a previously added set required a %s',
                $requires,
                $this->requires
            ));
        }
    }
}
