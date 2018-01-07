<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
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
     * @Check(["isSuccessfulLogic", "logic", "passesLogic"])
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
     * @Check(["passesAllOf", "passesAll", "logicAllOf"])
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
     * @Check(["passesNoneOf", "passesNone", "logicNoneOf", "failsAllOf"])
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
     * Check that none of the given branches succeed if given $value.
     *
     * @Check(["passesNoneOf", "passesNone", "logicNoneOf", "failsAllOf"])
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
    public function checkPassesAllOrNone($value, $branches, Manager $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\AllOrNone($manager ?: Manager::instance(), $branches),
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

    /**
     * Check conditional if-then-else clause.
     *
     * if the given $condition evaluates to true,
     * the $then must also evaluate to true,
     * otherwise the $else condition must evaluate to true.
     *
     * Examples of checking that if a person must be 18 years or
     * older in order to buy tobacco or alcohol.
     *
     * ```php
     * Ensure::that($product)
     *       ->condition($age < 18, 'isNotOneOf("tobacco", "alcohol")');
     *
     * Ensure::that($product)->if(
     *     Value::isOneOf('tobacco', 'alcohol'),
     *     $age >= 18
     * );
     *
     * Ensure::ifThen(
     *     $age < 18,
     *     Check::that($product)->isNotOneOf('tobacco', 'alcohol')
     * );
     *
     * Ensure::condition(
     *     Check::that($age)->isLowerThan(18),
     *     Check::that($product)->isNotOneOf('tobacco', 'alcohol')
     * );
     *
     * Ensure::that($product)->if(
     *     'isOneOf("tobacco", "alcohol")',
     *     $age >= 18
     * );
     * ```
     *
     * @Check(["if", "condition", "passesConditional", "ifThen", "ifThenElse"])
     *
     * @param mixed   $value     The value to be passed to the logic (if necessary)
     * @param mixed   $condition The condition to check
     * @param mixed   $then      The scenario that must pass if $condition passes.
     * @param mixed   $scenario  The scenario that must pass if $condition fails.
     * @param Manager $manager   The check provider manager to use.
     *                           If NULL, the default manager instance will be used
     * @param bool    $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkConditional($value, $condition, $then, $else = true, $manager = null, $withValue = true)
    {
        return $this->checkLogic(
            $withValue ? $value : null,
            new Logic\Conditional(
                $manager ?: Manager::instance(),
                $condition,
                $then,
                $else
            ),
            $withValue
        );
    }
}
