<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit;

use SplObjectStorage;
use UnexpectedValueException;
use Moccalotto\Valit\Contracts\CheckManager;
use Moccalotto\Valit\Contracts\CheckProvider;

class Manager implements CheckManager
{
    /**
     * @var Manager
     */
    protected static $instance;

    /**
     * Default checkers to load when created through create() or instance().
     *
     * @var array
     */
    protected static $defaultProviders = [
        Providers\XmlCheckProvider::class,
        Providers\DateCheckProvider::class,
        Providers\JsonCheckProvider::class,
        Providers\UuidCheckProvider::class,
        Providers\BasicCheckProvider::class,
        Providers\NumberCheckProvider::class,
        Providers\StringCheckProvider::class,
        Providers\ArrayCheckProvider::class,
        Providers\ObjectCheckProvider::class,
    ];

    /**
     * @var array
     */
    protected $checks = [];

    /**
     * Get the default (global) manager instance.
     *
     * If no instance is configured, a new one is created via the create() factory method.
     *
     * @return CheckManager
     *
     * @see create
     */
    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = static::create();
        }

        return static::$instance;
    }

    /**
     * Factory method.
     *
     * Create a Manager with all the default providers loaded
     * as well as the providers defined in $additionalCheckProviders
     *
     * @param string[] $additionalCheckProviders
     *
     * @return CheckManager
     */
    public static function create($additionalCheckProviders = [])
    {
        return new static(array_merge(static::$defaultProviders, $additionalCheckProviders));
    }

    /**
     * Constructor.
     *
     * @param string[] $providers array of provider FQCNs
     */
    public function __construct(array $providers)
    {
        foreach ($providers as $providerClass) {
            $this->loadProvider($providerClass);
        }
    }

    /**
     * Set this manager instance as the global one.
     *
     * Whenever you call Manager::instance(), the global instance is returned.
     */
    public function setAsGlobal()
    {
        static::$instance = $this;
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
            if (!$closures->contains($closure)) {
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
        if (!$this->hasCheck($checkName)) {
            throw new UnexpectedValueException(sprintf(
                'The check »%s« is not available',
                $checkName
            ));
        }
        $callback = $this->checks[$checkName];

        return call_user_func_array($callback, array_merge([$value], $args));
    }
}
