<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Manager;
use Valit\Logic\OneOf;
use Valit\Contracts\Logic;
use Valit\Result\AssertionResult;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class LogicCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $logic is successful.
     *
     * @Check(["isSuccessfulLogic", "logic", "passesLogic"])
     *
     * @param mixed $value     The value to be passed to the logic (if necessary)
     * @param Logic $logic     The logic to be executed
     * @param bool  $withValue Should $value be passed to the logic
     *
     * @return AssertionResult
     */
    public function checkLogic($value, Logic $logic, $withValue = true)
    {
        return $logic->execute((bool) $withValue, $withValue ? $value : null);
    }

    /**
     * Check that one of the given branches succeed of given $value.
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
            new OneOf($manager ?: Manager::instance(), $branches),
            $withValue
        );
    }
}
