<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Exceptions;

use BadMethodCallException;
use Valit\Result\ContainerResultBag;

/**
 * Exception thrown when a value is invalid.
 */
class InvalidContainerException extends InvalidValueException
{
    /**
     * Internal.
     *
     * @var ContainerResultBag
     */
    public $containerResults;

    /**
     * Constructor.
     *
     * @param ContainerResultBag $containerResults
     */
    public function __construct(ContainerResultBag $containerResults)
    {
        $this->containerResults = $containerResults;

        parent::__construct(
            $containerResults->varName,
            null,
            $containerResults->results
        );
    }

    /**
     * Forward calls to inner container.
     *
     * @param string $method Name of method to forward
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!is_callable([$this->containerResults, $method])) {
            throw new BadMethodCallException(sprintf(
                'The method »%s« does not exist on either %s or %s',
                $method,
                get_class($this),
                get_class($this->containerResults)
            ));
        }

        return call_user_func_array([$this->containerResults, $method], $args);
    }
}
