<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Exceptions;

use BadMethodCallException;
use UnexpectedValueException;
use Valit\Result\ContainerResultBag;

/**
 * Exception thrown when a value is invalid.
 */
class InvalidContainerException extends UnexpectedValueException
{
    /**
     * @var ContainerResultBag
     */
    protected $results;

    /**
     * Constructor.
     *
     * @param ContainerResultBag $results
     */
    public function __construct(ContainerResultBag $results)
    {
        $this->results = $results;

        parent::__construct(
            $this->getExpandedMessage(sprintf(
                '%s did not pass validation',
                ucfirst($results->varName)
            ))
        );
    }

    /**
     * Get details about why the exception failed.
     *
     * @param string $prefix
     *
     * @return string
     */
    public function getExpandedMessage($prefix)
    {
        $errorMessages = array_map(
            function ($result) {
                return sprintf(
                    '%s: %s',
                    $result->path,
                    $result->message
                );
            },
            $this->results->errors()
        );

        return $prefix
            .': '
            .PHP_EOL
            .'    '
            .implode(PHP_EOL.'    ', $errorMessages)
            .PHP_EOL
            .PHP_EOL;
    }

    /**
     * Get Results.
     *
     * @return ContainerResultBag
     */
    public function getResults()
    {
        return $this->results;
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
        if (!is_callable([$this->results, $method])) {
            throw new BadMethodCallException(sprintf(
                'The method »%s« does not exist on either %s or %s',
                $method,
                get_class($this),
                get_class($this->results)
            ));
        }

        return call_user_func_array([$this->results, $method], $args);
    }
}
