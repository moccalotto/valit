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

use SplObjectStorage;
use Moccalotto\Valit\Contracts\CheckManager;
use Moccalotto\Valit\Contracts\CheckProvider;

class Manager implements CheckManager
{
    /**
     * @var Manager
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $providers = [
        Providers\XmlCheckProvider::class,
        Providers\JsonCheckProvider::class,
        Providers\BasicCheckProvider::class,
        Providers\NumberCheckProvider::class,
        Providers\StringCheckProvider::class,
        Providers\UuidCheckProvider::class,
    ];

    /**
     * @var array
     */
    protected $checks = [];

    /**
     * Get or create the singleton instance.
     *
     * @return CheckManager
     */
    public static function instance()
    {
        if (! static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Constructor.
     */
    protected function __construct()
    {
        foreach ($this->providers as $providerClass) {
            $this->loadProvider($providerClass);
        }
    }

    /**
     * Load a provider and register all its checks.
     *
     * @param string $providerClass
     */
    protected function loadProvider($providerClass)
    {
        $provider = new $providerClass($this);

        $this->addProvider($provider);
    }

    /**
     * Get all checks.
     *
     * @return array
     */
    public function checks()
    {
        $closures = new SplObjectStorage();
        $checks = [];

        foreach ($this->checks as $alias => $closure) {
            if (! $closures->contains($closure)) {
                $check = new CheckMetaInfo($closure);
                $checks[] = $check;
                $closures->attach($closure, $check);
            }

            $check = $closures[$closure];

            $check->addAlias($alias);
        }

        return $checks;
    }

    /**
     * Add the checks from a provider to the manager.
     *
     * @param CheckProvider $provider
     */
    public function addProvider(CheckProvider $provider)
    {
        $this->checks += $provider->provides();
    }

    /**
     * Check if the manager can perform a given check.
     *
     * @param string $checkName
     *
     * @return bool
     */
    public function hasCheck($checkName)
    {
        return isset($this->checks[$checkName]);
    }

    /**
     * Execute a check.
     *
     * @param string $checkName
     * @param mixed  $value
     * @param array  $args
     *
     * @return Result
     */
    public function executeCheck($checkName, $value, array $args)
    {
        $callback = $this->checks[$checkName];

        return call_user_func_array($callback, array_merge([$value], $args));
    }
}
