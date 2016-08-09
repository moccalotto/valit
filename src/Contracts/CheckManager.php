<?php

namespace Moccalotto\Valit\Contracts;

interface CheckManager
{
    /**
     * Get or create the singleton instance.
     *
     * @return CheckManager
     */
    public static function instance();

    /**
     * Check if the manager can perform a given check.
     *
     * @param string $checkName
     *
     * @return bool
     */
    public function hasCheck($checkName);

    /**
     * Execute a check.
     *
     * @param string $checkName
     * @param mixed  $value
     * @param array  $args
     *
     * @return \Moccalotto\Valit\Result
     */
    public function executeCheck($checkName, $value, array $args);
}
