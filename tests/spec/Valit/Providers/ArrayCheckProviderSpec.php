<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Valit\Providers;

use ArrayObject;
use PhpSpec\ObjectBehavior;

class ArrayCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\ArrayCheckProvider');
    }

    function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    function it_checks_arrayAccess()
    {
        $this->checkArrayAccess([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('arrayable');
        $this->provides()->shouldHaveKey('isArrayable');
        $this->provides()->shouldHaveKey('hasArrayAccess');
        $this->provides()->shouldHaveKey('arrayAccessible');
        $this->provides()->shouldHaveKey('isArrayAccessible');

        $this->checkArrayAccess([])->success()->shouldBe(true);
        $this->checkArrayAccess(new ArrayObject())->success()->shouldBe(true);

        $this->checkArrayAccess(1)->success()->shouldBe(false);
        $this->checkArrayAccess(1.0)->success()->shouldBe(false);
        $this->checkArrayAccess(null)->success()->shouldBe(false);
        $this->checkArrayAccess('array')->success()->shouldBe(false);
        $this->checkArrayAccess(curl_init())->success()->shouldBe(false);
        $this->checkArrayAccess((object) [])->success()->shouldBe(false);
        $this->checkArrayAccess('ArrayObject')->success()->shouldBe(false);
    }

    function it_checks_associativeArray()
    {
        $this->checkAssociative([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('associative');
        $this->provides()->shouldHaveKey('isAssociative');
        $this->provides()->shouldHaveKey('associativeArray');
        $this->provides()->shouldHaveKey('isAssociativeArray');

        $this->checkAssociative(['' => 'b'])->success()->shouldBe(true);
        $this->checkAssociative(['a' => 'b'])->success()->shouldBe(true);
        $this->checkAssociative(['1a' => '1b'])->success()->shouldBe(true);
        $this->checkAssociative(['1.0' => '1.0'])->success()->shouldBe(true);
        $this->checkAssociative(['0.0' => '0.0'])->success()->shouldBe(true);
        $this->checkAssociative(['a' => 'a', '1' => '1'])->success()->shouldBe(true);
        $this->checkAssociative(['a' => 'a', 'b' => 'b'])->success()->shouldBe(true);

        $this->checkAssociative([])->success()->shouldBe(false);
        $this->checkAssociative([1 => '1b'])->success()->shouldBe(false);

        $this->checkAssociative(1)->success()->shouldBe(false);
        $this->checkAssociative(1.0)->success()->shouldBe(false);
        $this->checkAssociative(null)->success()->shouldBe(false);
        $this->checkAssociative('array')->success()->shouldBe(false);
        $this->checkAssociative(curl_init())->success()->shouldBe(false);
        $this->checkAssociative((object) [])->success()->shouldBe(false);
        $this->checkAssociative('ArrayObject')->success()->shouldBe(false);
    }

    function it_checks_strictArray()
    {
        $this->checkStrictArray([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('strictArray');
        $this->provides()->shouldHaveKey('isStrictArray');

        $this->checkStrictArray([])->success()->shouldBe(true);
        $this->checkStrictArray(['a'])->success()->shouldBe(true);
        $this->checkStrictArray(['a', 'b'])->success()->shouldBe(true);

        $this->checkStrictArray([999 => 999])->success()->shouldBe(false);
        $this->checkStrictArray(['999' => '999'])->success()->shouldBe(false);
        $this->checkStrictArray(['a' => 'a'])->success()->shouldBe(false);
        $this->checkStrictArray(['999.0' => '999.0'])->success()->shouldBe(false);
        $this->checkStrictArray(1)->success()->shouldBe(false);
        $this->checkStrictArray(1.0)->success()->shouldBe(false);
        $this->checkStrictArray(null)->success()->shouldBe(false);
        $this->checkStrictArray('array')->success()->shouldBe(false);
        $this->checkStrictArray(curl_init())->success()->shouldBe(false);
        $this->checkStrictArray((object) [])->success()->shouldBe(false);
        $this->checkStrictArray('ArrayObject')->success()->shouldBe(false);
    }


    function it_checks_numericArray()
    {
        $this->checkNumericIndex([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('hasNumericIndex');
        $this->provides()->shouldHaveKey('isConventionalArray');
        $this->provides()->shouldHaveKey('conventionalArray');
        $this->provides()->shouldHaveKey('isNotAssociative');
        $this->provides()->shouldHaveKey('notAssociative');

        $this->checkNumericIndex(['a'])->success()->shouldBe(true);
        $this->checkNumericIndex(['a', 'b'])->success()->shouldBe(true);
        $this->checkNumericIndex([999 => 999])->success()->shouldBe(true);
        $this->checkNumericIndex(['999' => '999'])->success()->shouldBe(true);

        $this->checkNumericIndex([])->success()->shouldBe(false);
        $this->checkNumericIndex(['a' => 'a'])->success()->shouldBe(false);
        $this->checkNumericIndex(['999.0' => '999.0'])->success()->shouldBe(false);

        $this->checkNumericIndex(1)->success()->shouldBe(false);
        $this->checkNumericIndex(1.0)->success()->shouldBe(false);
        $this->checkNumericIndex(null)->success()->shouldBe(false);
        $this->checkNumericIndex('array')->success()->shouldBe(false);
        $this->checkNumericIndex(curl_init())->success()->shouldBe(false);
        $this->checkNumericIndex((object) [])->success()->shouldBe(false);
        $this->checkNumericIndex('ArrayObject')->success()->shouldBe(false);
    }

    function it_checks_EmptyArray()
    {
        $this->checkEmpty([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('notEmpty');
        $this->provides()->shouldHaveKey('isNotEmpty');
        $this->provides()->shouldHaveKey('notEmptyArray');
        $this->provides()->shouldHaveKey('isNotEmptyArray');

        $this->checkEmpty([])->success()->shouldBe(true);


        $this->checkEmpty(1)->success()->shouldBe(false);
        $this->checkEmpty(1.0)->success()->shouldBe(false);
        $this->checkEmpty([0])->success()->shouldBe(false);
        $this->checkEmpty([1])->success()->shouldBe(false);
        $this->checkEmpty(null)->success()->shouldBe(false);
        $this->checkEmpty([null])->success()->shouldBe(false);
        $this->checkEmpty('array')->success()->shouldBe(false);
        $this->checkEmpty(curl_init())->success()->shouldBe(false);
        $this->checkEmpty((object) [])->success()->shouldBe(false);
        $this->checkEmpty('ArrayObject')->success()->shouldBe(false);
        $this->checkEmpty([null => null])->success()->shouldBe(false);
    }

    function it_checks_notEmptyArray()
    {
        $this->checkNotEmpty([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('notEmpty');
        $this->provides()->shouldHaveKey('isNotEmpty');
        $this->provides()->shouldHaveKey('notEmptyArray');
        $this->provides()->shouldHaveKey('isNotEmptyArray');

        $this->checkNotEmpty([0])->success()->shouldBe(true);
        $this->checkNotEmpty([1])->success()->shouldBe(true);
        $this->checkNotEmpty([null])->success()->shouldBe(true);
        $this->checkNotEmpty([null => null])->success()->shouldBe(true);

        $this->checkNotEmpty([])->success()->shouldBe(false);

        $this->checkNotEmpty(1)->success()->shouldBe(false);
        $this->checkNotEmpty(1.0)->success()->shouldBe(false);
        $this->checkNotEmpty(null)->success()->shouldBe(false);
        $this->checkNotEmpty('array')->success()->shouldBe(false);
        $this->checkNotEmpty(curl_init())->success()->shouldBe(false);
        $this->checkNotEmpty((object) [])->success()->shouldBe(false);
        $this->checkNotEmpty('ArrayObject')->success()->shouldBe(false);
    }

    function it_checks_unqiueValues()
    {
        $this->checkUniqueValues([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('hasUniqueValues');
        $this->provides()->shouldHaveKey('uniqueValues');

        $this->checkUniqueValues([])->success()->shouldBe(true);
        $this->checkUniqueValues([1, 2, 3])->success()->shouldBe(true);
        $this->checkUniqueValues(['a', 'b', 'c'])->success()->shouldBe(true);
        $this->checkUniqueValues(['a', 'b', 'c', 1, 2, 3])->success()->shouldBe(true);
        $this->checkUniqueValues(['a' => 1, 'b' => 2, 'c' => 3])->success()->shouldBe(true);

        $this->checkUniqueValues([1, 1])->success()->shouldBe(false);
        $this->checkUniqueValues(['', ''])->success()->shouldBe(false);
        $this->checkUniqueValues([1, 2, 3, 1])->success()->shouldBe(false);
        $this->checkUniqueValues(['a', 'b', 'c', 'a'])->success()->shouldBe(false);
        $this->checkUniqueValues(['a' => 1, 'b' => 1])->success()->shouldBe(false);

        $this->checkUniqueValues(1)->success()->shouldBe(false);
        $this->checkUniqueValues(1.0)->success()->shouldBe(false);
        $this->checkUniqueValues(null)->success()->shouldBe(false);
        $this->checkUniqueValues('array')->success()->shouldBe(false);
        $this->checkUniqueValues(curl_init())->success()->shouldBe(false);
        $this->checkUniqueValues((object) [])->success()->shouldBe(false);
        $this->checkUniqueValues('ArrayObject')->success()->shouldBe(false);
    }

    function it_checks_keyExists()
    {
        $this->checkKeyExists([], 1)->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('hasKey');
        $this->provides()->shouldHaveKey('keyExists');

        $this->checkKeyExists(['a' => 'b'], 'a')->success()->shouldBe(true);
        $this->checkKeyExists([0], 0)->success()->shouldBe(true);
        $this->checkKeyExists(new ArrayObject(['a']), 0)->success()->shouldBe(true);

        $this->checkKeyExists(['a' => 'b'], 0)->success()->shouldBe(false);
        $this->checkKeyExists([], 0)->success()->shouldBe(false);
        $this->checkKeyExists((object) [0], 0)->success()->shouldBe(false);
        $this->checkKeyExists('kim', 0)->success()->shouldBe(false);
    }

    /*
    function it_checks_nestedKeyExists()
    {
        // TODO: implement
        // Check::that($foo)->hasNestKey('root.branch.twig.leaf')
        // Check::that($foo)->hasNestKey('root/branch/twig/leaf', '/')
    }

    function it_checks_nestedValue()
    {
        // TODO: implement
    }

    function it_checks_nestedValuePassesCheck()
    {
        // TODO: implement
        // Check::that($foo)->nestedValuePasses('root.branch.twig.leaf', '.', 'isString')
        // Check::that($foo)->nestedValuePasses('root.node->twig.node->leaf.node', '->', 'matches', '/someRegex/A')
    }
     */
}
