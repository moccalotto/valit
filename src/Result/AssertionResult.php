<?php

namespace Valit\Result;

use Valit\Util\Val;

/**
 * Result of executing a single assertion.
 */
class AssertionResult
{
    /**
     * @var bool
     *
     * @internal
     */
    public $success;

    /**
     * @var string
     *
     * @internal
     */
    public $message;

    /**
     * @var array
     *
     * @internal
     */
    public $context;

    /**
     * Constructor.
     *
     * @param bool   $success
     * @param string $message
     * @param array  $context
     */
    public function __construct($success, $message, array $context = [])
    {
        $this->success = (bool) $success;
        $this->message = (string) $message;
        $this->context = $context;
    }

    /**
     * Did the validation succeed?
     *
     * @return bool
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * Get the error message template.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Get the contextual variables connected to the check.
     *
     * @return array
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * Render the error mesage, using the name and alias provided.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return string
     */
    public function renderMessage($name, $value)
    {
        $context = $this->context;
        $context['value'] = $value;

        return preg_replace_callback(
            '/\{([a-z0-9_]+)(?::([a-z0-9_]+))?\}/ui',
            function ($matches) use ($context, $name) {
                $all = $matches[0];
                $key = $matches[1];
                $fmt = isset($matches[2]) ? $matches[2] : 'normal';

                if ($key === 'name') {
                    return $name;
                }

                if (!isset($context[$key])) {
                    return $all;
                }

                return Val::format($context[$key], $fmt);
            },
            $this->message
        );
    }
}
