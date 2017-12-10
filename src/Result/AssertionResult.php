<?php

namespace Valit\Result;

use LogicException;
use RuntimeException;

/**
 * Valit Result.
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
        $this->message = $message;
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
     * Format a value for being displayed as a string in an error message.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function escape($value)
    {
        if (is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (is_callable($value)) {
            return 'Callable';
        }

        if (is_resource($value)) {
            return sprintf('%s (%s)', $value, get_resource_type($value));
        }

        if (is_object($value)) {
            return sprintf('Object (%s)', get_class($value));
        }

        if (is_array($value)) {
            return sprintf('Array (%d entries)', count($value));
        }

        if (is_null($value)) {
            return 'null';
        }

        throw new RuntimeException(sprintf(
            'Unknown type: %s',
            gettype($value)
        ));
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

                return $this->format($context[$key], $fmt);
            },
            $this->message
        );
    }

    /**
     * Format a given value into a string.
     *
     * @param mixed  $value
     * @param string $format
     *
     * @return string
     *
     * @throws LogicException if $format is not known
     */
    protected function format($value, $format)
    {
        if ($format === 'normal') {
            return $this->escape($value);
        }

        if ($format === 'raw') {
            return is_scalar($value) || is_callable([$value, '__toString'])
                ? (string) $value
                : $this->escape($value);
        }

        if ($format === 'type') {
            return gettype($value);
        }

        if ($format === 'float') {
            return is_numeric($value)
                ? sprintf('%g', $value)
                : '[not numeric]';
        }
        if ($format === 'hex') {
            return is_int($value) || ctype_digit($value)
                ? sprintf('%x', $value)
                : '[not integer]';
        }

        throw new LogicException("Unknown format »{$format}«");
    }
}
