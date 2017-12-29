<?php

namespace Valit\Result;

use Valit\Util\Val;
use Valit\Contracts\Result;

/**
 * Result of executing a single assertion.
 */
class AssertionResult implements Result
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
     * @var string|null
     *
     * @internal
     */
    public $path;

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
        $this->path = null;
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
     * Get the path of this result.
     *
     * Only assertions made on container fields have paths.
     *
     * @return string|null
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Set a path on the object for injection into a ContainerValidator.
     *
     * @return self
     */
    public function withPath($path)
    {
        Val::mustBe($path, 'string|null');

        $clone = clone $this;

        $clone->path = $path;

        return $clone;
    }

    /**
     * Normalize an AssertionResult for injection
     * into a result container.
     *
     * @param string $varName
     * @param mixed  $value
     *
     * @return self
     */
    public function normalize($varName, $value)
    {
        $varName = Val::toString($varName);

        $template = $this->message;
        $context = $this->context;
        $context['value'] = $value;

        $message = preg_replace_callback(
            '/\{([a-z0-9_]+)(?::([a-z0-9_]+))?\}/ui',
            function ($matches) use ($context, $varName) {
                $all = $matches[0];
                $key = $matches[1];
                $fmt = isset($matches[2]) ? $matches[2] : 'normal';

                if ($key === 'name') {
                    return $varName;
                }

                if (!isset($context[$key])) {
                    return $all;
                }

                return Val::format($context[$key], $fmt);
            },
            $template
        );

        return new self(
            $this->success,
            $message
        );
    }
}
