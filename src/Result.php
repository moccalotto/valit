<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit;

/**
 * Valit Result.
 */
class Result
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $context;

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
    protected function formatValue($value)
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
            return sprintf('Array (%d entries', count($value));
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
    public function renderErrorMessage($name, $value)
    {
        $context = $this->context;
        $context['value'] = $value;

        $replacecments = ['{name}' => $name];

        foreach ($context as $key => $value) {
            $raw = is_scalar($value) || is_callable([$value, '__toString'])
                ? (string) $value
                : $this->formatValue($value);
            $replacecments[sprintf('{%s}', $key)] = $this->formatValue($value);
            $replacecments[sprintf('{%s:raw}', $key)] = $raw;
            $replacecments[sprintf('{%s:type}', $key)] = gettype($value);
            $replacecments[sprintf('{%s:float}', $key)] = is_numeric($value) ? sprintf('%g', $value) : '[not numeric]';
            $replacecments[sprintf('{%s:hex}', $key)] = ctype_digit($value) ? sprintf('%x', $value) : '[not numeric]';
        }

        return strtr($this->message, $replacecments);
    }
}
