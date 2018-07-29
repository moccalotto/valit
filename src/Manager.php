<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit;

use SplObjectStorage;
use Valit\Util\CheckInfo;
use UnexpectedValueException;
use Valit\Contracts\CheckProvider;

/**
 * Container for all available checks.
 */
class Manager
{
    /**
     * Internal.
     *
     * @var Manager
     */
    public static $instance;

    /**
     * Internal: Default checkers to load when created through create() or instance().
     *
     * @var array
     */
    public static $defaultProviders = [
        Providers\CustomCheckProvider::class,
        Providers\XmlCheckProvider::class,
        Providers\DateCheckProvider::class,
        Providers\JsonCheckProvider::class,
        Providers\UuidCheckProvider::class,
        Providers\BasicCheckProvider::class,
        Providers\NumberCheckProvider::class,
        Providers\StringCheckProvider::class,
        Providers\ArrayCheckProvider::class,
        Providers\ObjectCheckProvider::class,
        Providers\UriCheckProvider::class,
        Providers\LogicCheckProvider::class,
        Providers\FileSystemCheckProvider::class,
    ];

    /**
     * Internal.
     *
     * @var array
     */
    public $checks = [];

    /**
     * Get the default (global) manager instance.
     *
     * If no instance is configured, a new one is created via the create() factory method.
     *
     * @return Manager
     *
     * @see create
     */
    public static function instance()
    {
        if (static::$instance === null) {
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
     * @return Manager
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
            if (!class_exists($providerClass)) {
                throw new UnexpectedValueException(sprintf('Class »%s« does not exist', $providerClass));
            }
            if (!is_a($providerClass, CheckProvider::class, true)) {
                throw new UnexpectedValueException(sprintf(
                    'Class »%s« does not implement %s',
                    $providerClass,
                    CheckProvider::class
                ));
            }
            $this->addProvider(new $providerClass($this));
        }
    }

    /**
     * Set this manager instance as the global one.
     *
     * Whenever you call Manager::instance(), the global instance is returned.
     *
     * @return $this
     */
    public function setGlobal()
    {
        static::$instance = $this;

        return $this;
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
                $check = new CheckInfo($closure);
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
     * @return \Valit\Result\AssertionResult
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

    /**
     * Debug info.
     *
     * This method reduces the size of print_r and var_dump()
     *
     * @return array
     */
    public function __debugInfo()
    {
        return ['checkCount' => count($this->checks)];
    }
}
