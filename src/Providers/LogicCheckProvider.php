<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Logic;
use Valit\Manager;
use Valit\Result\AssertionResult;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;
use Valit\Contracts\Logic as LogicContract;

class LogicCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $logic is successful.
     *
     * @Check(["passes", "isSuccessfulLogic", "logic", "passesLogic"])
     *
     * @param mixed         $value     The value to be passed to the logic (if necessary)
     * @param LogicContract $logic     The logic to be executed
     * @param bool          $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkLogic($value, LogicContract $logic, $withValue = true)
    {
        return $logic->execute((bool) $withValue, $withValue ? $value : null);
    }

    /**
     * Check that one (and only one) of the given branches succeed if given $value.
     *
     * @Check(["passesOneOf", "logicOneOf"])
     *
     * @param mixed   $value     The value to be passed to the logic (if necessary)
     * @param array   $branches  The branches of the logic
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkPassesOneOf($value, $branches, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\OneOf($manager ?: Manager::instance(), $branches),
            $withValue
        );
    }

    /**
     * Check that at least one of the given branches succeed if given $value.
     *
     * @Check(["passesAnyOf", "logicAnyOf"])
     *
     * @param mixed   $value     The value to be passed to the logic (if necessary)
     * @param array   $branches  The branches of the logic
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkPassesAnyOf($value, $branches, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\AnyOf($manager ?: Manager::instance(), $branches),
            $withValue
        );
    }

    /**
     * Check that all of the given branches succeed if given $value.
     *
     * @Check(["passesAllOf", "logicAllOf"])
     *
     * @param mixed   $value     The value to be passed to the logic (if necessary)
     * @param array   $branches  The branches of the logic
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkPassesAllOf($value, $branches, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\AllOf($manager ?: Manager::instance(), $branches),
            $withValue
        );
    }

    /**
     * Check that none of the given branches succeed if given $value.
     *
     * @Check(["passesNoneOf", "logicNoneOf", "failsAllOf"])
     *
     * @param mixed   $value     The value to be passed to the logic (if
     *                           necessary)
     * @param array   $branches  The branches of the logic
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkPassesNoneOf($value, $branches, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\NoneOf($manager ?: Manager::instance(), $branches),
            $withValue
        );
    }

    /**
     * Check that the given scenario does not succeed if given $value.
     *
     * @Check(["doesNotPass", "not", "fails", "invert"])
     *
     * @param mixed   $value     The value to be passed to the logic (if necessary)
     * @param array   $scenario  The scenario that must fail
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkDoesNotPass($value, $scenario, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\Not($manager ?: Manager::instance(), $scenario),
            $withValue
        );
    }
}
