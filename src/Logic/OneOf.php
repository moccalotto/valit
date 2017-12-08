<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Logic;

use Valit\Manager;
use LogicException;
use Valit\Contracts\CheckManager;

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

        foreach ($checks as $key => $value) {
            if (is_string($key)) {
                $this->addContainerCheck($key, $value);
                return;
            }
            if ($value instanceof Logic) {
                $this->addLogicCheck($value);
                return;
            }

            if ($value instanceof Template) {
                $this->addTemplateCheck($key, $value);
                return;
            }

            if ($value instanceof FluentCheckInterface) {
                $this->addFluent($value);
                return;
            }

            throw new LogicException(sprintf('Unknown logic check: %s', $value));
        }
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

    public function addContainerCheck($fieldNameGlob, $checks)
    {
        $this->addRequirement(static::REQUIRES_CONTAINER);

        $this->checks[] = ['container', $fieldNameGlob, $checks];
    }

    public function addLogicCheck(Logic $logic)
    {
        $this->addRequirement($logic->requires);

        $this->checks[] = ['logic', $logic];
    }

    public function addTemplateCheck(Template $template)
    {
        $this->addRequirement(static::REQUIRES_VALUE);

        $this->checks[] = ['template', $template];
    }

    public function addFluent(FluentCheckInterface $fluent)
    {
        $this->checks[] = ['fluent', $fluent];
    }

    public function execute($value = null, $varName = null, CheckManager $manager = null)
    {
        if ($manager === null) {
            $manager = Manager::instance();
        }

        $results = new ResultBag();

        foreach ($this->checks as $args) {
            $type = array_shift($args);

            switch ($type) {
                case 'container':
                    list ($fieldNameGlob, $checks) = $args;
                    $this->executeOnContainer(
                        $results,
                        $manager,
                        $varName,
                        $value,
                        $fieldNameGlob,
                        $checks
                    );
                    break;
                case 'logic':
                    $this->executeLogic(
                        $results,
                        $manager,
                        $varName,
                        $value,
                        $args[0]
                    );
                    break;
                case 'template':
                    $this->executeTemplate(
                        $results,
                        $manager,
                        $varName,
                        $value,
                        $args[0]
                    );
                    break;
                case 'fluent':
                    $this->addResultsFromFluent(
                        $results,
                        $manager,
                        $varName,
                        $value,
                        $args[0]
                    );
                    break;
                default:
                    throw new LogicException('This should not happen!');
            }
        }
    }
}
