<?php

namespace Valit;

/**
 * Class for executing custom callbacks.
 */
class CustomCallbackChecker implements CustomChecker
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $context;

    /**
     * Constructor.
     *
     * @param string   $message
     * @param callable $callback
     */
    public function __construct($message, $callback, $context = [])
    {
        $this->message = (string) $message;
        $this->callback = $callback;
        $this->context = $context;
    }

    /**
     * Execute the check.
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function check($value)
    {
        $success = (bool) call_user_func($this->callback, $value);

        return new Result($success, $this->message, $this->context);
    }
}
