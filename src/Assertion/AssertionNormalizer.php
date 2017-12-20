<?php

namespace Valit\Assertion;

use LogicException;
use Valit\Contracts\Logic;

class AssertionNormalizer
{
    /**
     * @var AssertionBag
     *
     * @internal
     */
    public $assertions;

    /**
     * Constructor.
     *
     * @param string|array|Template|AssertionBag $assertions
     */
    public function __construct($assertions)
    {
        $this->normalizeAndSet($assertions);
    }

    /**
     * Return a normalized version of $assertions.
     *
     * @param string|array|Template|AssertionBag $assertions
     *
     * @return AssertionBag
     */
    public static function normalize($assertions)
    {
        return (new static($assertions))->assertions;
    }

    /**
     * Get all assertions (except the "required" and "optional" pseudo-assertions).
     *
     * @return AssertionBag
     */
    public function all()
    {
        return $this->assertions;
    }

    /**
     * Normalize a set of assertions and add it to $this->assertions.
     *
     * Assertions can be given as a string, an array, a Template or an AssertionBag.
     * When string-encoded, the string contains a number of assertion-expressions separated by ampersands.
     * When associative array, each key=>value pair can either be checkName => parameters
     * When numeric array, each entry contains a single assertion-expression.
     *
     * We normalize them into well-behaved arrays of checkName => parameters.
     *
     * @param string|array|Template|AssertionBag $assertions
     */
    protected function normalizeAndSet($assertions)
    {
        if (is_a($assertions, Template::class)) {
            $this->assertions = clone $assertions->assertions;

            return;
        }
        if (is_a($assertions, AssertionBag::class)) {
            $this->assertions = clone $assertions;

            return;
        }
        if (is_a($assertions, Logic::class)) {
            $this->assertions = new AssertionBag([
                new Assertion('passesLogic', [
                    $assertions,
                    true,
                ]),
            ]);

            $this->assertions->setFlag('optional', true);

            return;
        }
        if (empty($assertions)) {
            $this->assertions = new AssertionBag();

            return;
        }

        if (!is_array($assertions)) {
            // turn a assertion-expression into an array of single assertion-expressions.
            $assertions = array_map(
                function ($str) {
                    return str_replace('&&', '&', $str);
                },
                preg_split('/\s*(?<!&)&(?!&)\s*/u', (string) $assertions)
            );
        }

        $this->assertions = new AssertionBag();

        foreach ($assertions as $k => $v) {
            $this->parseAndAdd($k, $v);
        }
    }

    /**
     * Parse a single assertion-expression.
     *
     * @param int|string $key
     * @param mixed      $args
     *
     * @return array containing [$checkName, $args]
     */
    protected function parseAndAdd($key, $args)
    {
        if (is_int($key) && is_string($args)) {
            // Example:
            // --------
            // $key: 0
            // $args: "isGreaterThan(0)"
            $expr = $args;
        } elseif (is_int($key) && is_array($args)) {
            // Example 1:
            // ----------
            // $key: 42
            // $args: ["isGreaterThan" => [0]]

            // Example 2:
            // ----------
            // $key: 1987
            // $args: ["isGreaterThan(0)"]
            $expr = array_shift($args);
        } else {
            // Example:
            // --------
            // $key:  "isGreaterThan"
            // $args: [0]
            $expr = $key;
        }

        if ($expr === '') {
            return [null, null];
        }

        if (!is_string($expr)) {
            throw new LogicException(sprintf('Invalid assertion at index %d', $key));
        }

        if (!preg_match('/([a-z0-9]+)\s*(?:\((.*?)\))?$/Aui', $expr, $matches)) {
            throw new LogicException(sprintf('Invalid expression »%s«', $expr));
        }

        if (isset($matches[2])) {
            $args = json_decode(sprintf('[%s]', $matches[2]));
        }

        $this->addSingleAssertion(
            $matches[1],     // check name
            (array) $args    // assertion args
        );
    }

    /**
     * Add a single assertion to our array of assertions.
     *
     * @param string $checkName
     * @param array  $assertionArgs
     */
    public function addSingleAssertion($checkName, $assertionArgs)
    {
        if (in_array($checkName, ['optional', 'isOptional'])) {
            $this->assertions->setFlag('optional', true);

            return;
        }
        if (in_array($checkName, ['required', 'isRequired', 'present', 'isPresent'])) {
            $this->assertions->setFlag('optional', false);

            return;
        }

        $this->assertions->add(
            new Assertion($checkName, $assertionArgs)
        );
    }
}
