<?php

namespace Valit\Util;

use Closure;
use InvalidArgumentException;
use Valit\Result\AssertionResult;
use Valit\Contracts\CustomChecker;

/**
 * Class for executing custom callbacks.
 */
class CallbackChecker implements CustomChecker
{
    /**
     * Internal.
     *
     * @var string
     */
    public $message;

    /**
     * Internal.
     *
     * @var Closure
     */
    public $callback;

    /**
     * Internal.
     *
     * @var array
     */
    public $context;

    /**
     * Constructor.
     *
     * @param string   $message
     * @param callable $callback
     * @param array    $context
     *
     * @throws InvalidArgumentException if $callback is not callable
     */
    public function __construct($message, $callback, $context = [])
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Second argument must be callable');
        }
        $this->message = Val::toString($message);
        $this->callback = Val::toClosure($callback);
        $this->context = $context;
    }

    /**
     * Execute the check.
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function check($value)
    {
        $success = (bool) call_user_func($this->callback, $value);

        return new AssertionResult($success, $this->message, $this->context);
    }

    /**
     * Info for print_r.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'callback' => Val::formatCallback($this->callback),
            'message' => $this->message,
            'context' => $this->context,
        ];
    }
}
