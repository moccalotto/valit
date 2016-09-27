<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit\Providers;

use ArrayObject;
use PhpSpec\ObjectBehavior;

class ArrayCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\ArrayCheckProvider');
    }

    public function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    public function it_checks_arrayAccess()
    {
        $this->checkArrayAccess([])->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('hasArrayAccess');
        $this->provides()->shouldHaveKey('arrayAccessible');

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

    public function it_checks_associativeArray()
    {
        $this->checkAssociative([])->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('associative');
        $this->provides()->shouldHaveKey('isAssociative');
        $this->provides()->shouldHaveKey('associativeArray');
        $this->provides()->shouldHaveKey('isAssociativeArray');

        $this->checkAssociative(['' => 'b'])->success()->shouldBe(true);
        $this->checkAssociative(['a' => 'b'])->success()->shouldBe(true);
        $this->checkAssociative(['1a' => '1b'])->success()->shouldBe(true);
        $this->checkAssociative(['1.0' => '1.0'])->success()->shouldBe(true);
        $this->checkAssociative(['0.0' => '0.0'])->success()->shouldBe(true);
        $this->checkAssociative(['a' => 'a', 'b' => 'b'])->success()->shouldBe(true);

        $this->checkAssociative([])->success()->shouldBe(false);
        $this->checkAssociative([1 => '1b'])->success()->shouldBe(false);
        $this->checkAssociative(['a' => 'a', '1' => '1'])->success()->shouldBe(false);

        $this->checkAssociative(1)->success()->shouldBe(false);
        $this->checkAssociative(1.0)->success()->shouldBe(false);
        $this->checkAssociative(null)->success()->shouldBe(false);
        $this->checkAssociative('array')->success()->shouldBe(false);
        $this->checkAssociative(curl_init())->success()->shouldBe(false);
        $this->checkAssociative((object) [])->success()->shouldBe(false);
        $this->checkAssociative('ArrayObject')->success()->shouldBe(false);
    }

    public function it_checks_numericArray()
    {
        $this->checkNumericIndex([])->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_notEmptyArray()
    {
        $this->checkNotEmpty([])->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isNotEmptyArray');
        $this->provides()->shouldHaveKey('notEmptyArray');

        $this->checkNotEmpty([0])->success()->shouldBe(true);
        $this->checkNotEmpty([1])->success()->shouldBe(true);
        $this->checkNotEmpty([null])->success()->shouldBe(true);
        $this->checkNotEmpty([null => null])->success()->shouldBe(true);

        $this->checkNotEmpty([])->success()->shouldBe(false);

        $this->checkNumericIndex(1)->success()->shouldBe(false);
        $this->checkNumericIndex(1.0)->success()->shouldBe(false);
        $this->checkNumericIndex(null)->success()->shouldBe(false);
        $this->checkNumericIndex('array')->success()->shouldBe(false);
        $this->checkNumericIndex(curl_init())->success()->shouldBe(false);
        $this->checkNumericIndex((object) [])->success()->shouldBe(false);
        $this->checkNumericIndex('ArrayObject')->success()->shouldBe(false);
    }

    public function it_checks_unqiueValues()
    {
        $this->checkUniqueValues([])->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('hasUniqueValues');
        $this->provides()->shouldHaveKey('uniqueValues');

        $this->checkUniqueValues([])->success()->shouldBe(true);
        $this->checkUniqueValues([1,2,3])->success()->shouldBe(true);
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
