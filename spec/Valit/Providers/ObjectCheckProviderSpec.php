<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Valit\Providers;

use ArrayObject;
use Exception;
use InfiniteIterator;
use PhpSpec\ObjectBehavior;

class ObjectCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\ObjectCheckProvider');
    }

    function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    function it_checks_objectOrClass()
    {
        $this->provides()->shouldHaveKey('objectOrClass');
        $this->provides()->shouldHaveKey('isObjectOrClass');

        $this->checkObjectOrClass(null)->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->checkObjectOrClass((object) [])->success()->shouldBe(true);
        $this->checkObjectOrClass(Exception::class)->success()->shouldBe(true);
        $this->checkObjectOrClass(json_decode('{}'))->success()->shouldBe(true);
        $this->checkObjectOrClass('SimpleXmlElement')->success()->shouldBe(true);
        $this->checkObjectOrClass('Valit\Result\SingleAssertionResult')->success()->shouldBe(true);

        $this->checkObjectOrClass(1)->success()->shouldBe(false);
        $this->checkObjectOrClass([])->success()->shouldBe(false);
        $this->checkObjectOrClass(NAN)->success()->shouldBe(false);
        $this->checkObjectOrClass(null)->success()->shouldBe(false);
        $this->checkObjectOrClass('test')->success()->shouldBe(false);
        $this->checkObjectOrClass('Iterator')->success()->shouldBe(false);
        $this->checkObjectOrClass('Countable')->success()->shouldBe(false);
        $this->checkObjectOrClass(curl_init())->success()->shouldBe(false);
        $this->checkObjectOrClass('Traversable')->success()->shouldBe(false);
        $this->checkObjectOrClass(NonExisting::class)->success()->shouldBe(false);
        $this->checkObjectOrClass('Valit\Contracts\CheckManager')->success()->shouldBe(false);
    }

    function it_checks_className()
    {
        $this->provides()->shouldHaveKey('isClass');
        $this->provides()->shouldHaveKey('isOfClass');
        $this->provides()->shouldHaveKey('className');
        $this->provides()->shouldHaveKey('isClassName');

        $this->checkClassName(null)->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->checkClassName(Exception::class)->success()->shouldBe(true);
        $this->checkClassName('SimpleXmlElement')->success()->shouldBe(true);
        $this->checkClassName('Valit\Result\SingleAssertionResult')->success()->shouldBe(true);

        $this->checkClassName(1)->success()->shouldBe(false);
        $this->checkClassName([])->success()->shouldBe(false);
        $this->checkClassName(NAN)->success()->shouldBe(false);
        $this->checkClassName(null)->success()->shouldBe(false);
        $this->checkClassName('test')->success()->shouldBe(false);
        $this->checkClassName((object) [])->success()->shouldBe(false);
        $this->checkClassName(curl_init())->success()->shouldBe(false);
        $this->checkClassName(json_decode('{}'))->success()->shouldBe(false);
        $this->checkClassName(NonExisting::class)->success()->shouldBe(false);
    }

    function it_checks_interfaceName()
    {
        $this->provides()->shouldHaveKey('isInterface');
        $this->provides()->shouldHaveKey('interfaceName');
        $this->provides()->shouldHaveKey('isInterfaceName');

        $this->checkInterfaceName(null)->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->checkInterfaceName('Iterator')->success()->shouldBe(true);
        $this->checkInterfaceName('Countable')->success()->shouldBe(true);
        $this->checkInterfaceName('Traversable')->success()->shouldBe(true);
        $this->checkInterfaceName('Valit\Contracts\CheckManager')->success()->shouldBe(true);

        $this->checkInterfaceName(1)->success()->shouldBe(false);
        $this->checkInterfaceName([])->success()->shouldBe(false);
        $this->checkInterfaceName(NAN)->success()->shouldBe(false);
        $this->checkInterfaceName(null)->success()->shouldBe(false);
        $this->checkInterfaceName('test')->success()->shouldBe(false);
        $this->checkInterfaceName((object) [])->success()->shouldBe(false);
        $this->checkInterfaceName(curl_init())->success()->shouldBe(false);
        $this->checkInterfaceName(Exception::class)->success()->shouldBe(false);
        $this->checkInterfaceName(json_decode('{}'))->success()->shouldBe(false);
        $this->checkInterfaceName('SimpleXmlElement')->success()->shouldBe(false);
        $this->checkInterfaceName(NonExisting::class)->success()->shouldBe(false);
        $this->checkInterfaceName('Valit\Result\SingleAssertionResult')->success()->shouldBe(false);
        $this->checkInterfaceName('Valit\Traits\ProvideViaReflection')->success()->shouldBe(false);
    }

    function it_checks_traitsName()
    {
        $this->provides()->shouldHaveKey('isTrait');
        $this->provides()->shouldHaveKey('traitName');
        $this->provides()->shouldHaveKey('isTraitName');

        $this->checkTraitName(null)->shouldHaveType('Valit\Result\SingleAssertionResult');

        eval('trait FooBarBazSpecTrait {}');
        $this->checkTraitName('FooBarBazSpecTrait')->success()->shouldBe(true);
        $this->checkTraitName('Valit\Traits\ProvideViaReflection')->success()->shouldBe(true);

        $this->checkTraitName(1)->success()->shouldBe(false);
        $this->checkTraitName([])->success()->shouldBe(false);
        $this->checkTraitName(NAN)->success()->shouldBe(false);
        $this->checkTraitName(null)->success()->shouldBe(false);
        $this->checkTraitName('test')->success()->shouldBe(false);
        $this->checkTraitName('Iterator')->success()->shouldBe(false);
        $this->checkTraitName('Countable')->success()->shouldBe(false);
        $this->checkTraitName((object) [])->success()->shouldBe(false);
        $this->checkTraitName(curl_init())->success()->shouldBe(false);
        $this->checkTraitName('Traversable')->success()->shouldBe(false);
        $this->checkTraitName(Exception::class)->success()->shouldBe(false);
        $this->checkTraitName(json_decode('{}'))->success()->shouldBe(false);
        $this->checkTraitName('SimpleXmlElement')->success()->shouldBe(false);
        $this->checkTraitName(NonExisting::class)->success()->shouldBe(false);
        $this->checkTraitName('Valit\Result\SingleAssertionResult')->success()->shouldBe(false);
        $this->checkTraitName('Valit\Contracts\CheckManager')->success()->shouldBe(false);
    }

    function it_checks_instanceof()
    {
        $this->checkInstanceOf(null, '')->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->provides()->shouldHaveKey('isInstanceOf');

        $realThis = $this->getWrappedObject();
        $this->checkInstanceOf((object) [], 'StdClass')->success()->shouldBe(true);
        $this->checkInstanceOf(new Exception(), 'Exception')->success()->shouldBe(true);
        $this->checkInstanceOf($realThis, get_class($realThis))->success()->shouldBe(true);
        $this->checkInstanceOf(new ArrayObject(), 'ArrayAccess')->success()->shouldBe(true);
        $this->checkInstanceOf(new Exception(), Exception::class)->success()->shouldBe(true);
        $this->checkInstanceOf(new InfiniteIterator((new ArrayObject())->getIterator()), 'IteratorIterator')->success()->shouldBe(true);

        $this->checkInstanceOf(1, 'StdClass')->success()->shouldBe(false);
        $this->checkInstanceOf([], 'StdClass')->success()->shouldBe(false);
        $this->checkInstanceOf(NAN, 'StdClass')->success()->shouldBe(false);
        $this->checkInstanceOf(null, 'StdClass')->success()->shouldBe(false);
        $this->checkInstanceOf('test', 'StdClass')->success()->shouldBe(false);
        $this->checkInstanceOf('Iterator', 'Iterator')->success()->shouldBe(false);
        $this->checkInstanceOf(curl_init(), 'Resource')->success()->shouldBe(false);
        $this->checkInstanceOf('Countable', 'Countable')->success()->shouldBe(false);
        $this->checkInstanceOf('Traversable', 'Traversable')->success()->shouldBe(false);
        $this->checkInstanceOf(new ArrayObject(), 'Iterator')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, 1.0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, false]);
        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, curl_init()]);
        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkInstanceOf', [null, (object) []]);
    }

    function it_checks_implementing()
    {
        $this->checkImplements(null, 'Iterator')->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->provides()->shouldHaveKey('implements');
        $this->provides()->shouldHaveKey('isImplementing');

        if (interface_exists('Throwable')) {
            $this->checkImplements(new Exception(), 'Throwable')->success()->shouldBe(true);
        }
        $this->checkImplements(new ArrayObject(), 'ArrayAccess')->success()->shouldBe(true);
        $this->checkImplements(new ArrayObject(), 'IteratorAggregate')->success()->shouldBe(true);
        $this->checkImplements((new ArrayObject())->getIterator(), 'Iterator')->success()->shouldBe(true);

        $this->checkImplements(1, 'Iterator')->success()->shouldBe(false);
        $this->checkImplements([], 'Iterator')->success()->shouldBe(false);
        $this->checkImplements(NAN, 'Iterator')->success()->shouldBe(false);
        $this->checkImplements(null, 'Iterator')->success()->shouldBe(false);
        $this->checkImplements('test', 'Iterator')->success()->shouldBe(false);
        $this->checkImplements('Iterator', 'Iterator')->success()->shouldBe(false);
        $this->checkImplements(curl_init(), 'Iterator')->success()->shouldBe(false);
        $this->checkImplements('Countable', 'Countable')->success()->shouldBe(false);
        $this->checkImplements('Traversable', 'Traversable')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, 1.0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, false]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, curl_init()]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkImplements', [null, 'StdClass']);
    }

    function it_checks_hasMethod()
    {
        $this->checkHasMethod(null, '')->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->provides()->shouldHaveKey('hasMethod');

        $this->checkHasMethod($this, 'provides')->success()->shouldBe(true);
        $this->checkHasMethod('Exception', 'getMessage')->success()->shouldBe(true);
        $this->checkHasMethod(new Exception(), 'getMessage')->success()->shouldBe(true);
        $this->checkHasMethod('Valit\Traits\ProvideViaReflection', 'provides')->success()->shouldBe(true);
        $this->checkHasMethod('ArrayObject', 'getIterator')->success()->shouldBe(true);
        $this->checkHasMethod(new ArrayObject(), 'getIterator')->success()->shouldBe(true);
        $this->checkHasMethod('IteratorAggregate', 'getIterator')->success()->shouldBe(true);

        $this->checkHasMethod('IteratorAggregate', 'nonExistingMethod')->success()->shouldBe(false);
        $this->checkHasMethod('NonExistingClass', '__toString')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', 1]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', 1.0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasMethod', ['Iterator', curl_init()]);
    }

    function it_checks_hasProperty()
    {
        $this->checkHasMethod(null, '')->shouldHaveType('Valit\Result\SingleAssertionResult');

        $this->provides()->shouldHaveKey('hasProperty');

        $fooObject = eval(<<<'PHP'
class FooCheckClass {
    public    $pub;
    protected $pro;
    private   $pri;

    static public    $pubStat;
    static protected $proStat;
    static private   $priStat;
}
return new FooCheckClass();
PHP
    );

        $this->checkHasProperty($fooObject, 'pri')->success()->shouldBe(true);
        $this->checkHasProperty($fooObject, 'pro')->success()->shouldBe(true);
        $this->checkHasProperty($fooObject, 'pub')->success()->shouldBe(true);
        $this->checkHasProperty($fooObject, 'priStat')->success()->shouldBe(true);
        $this->checkHasProperty($fooObject, 'proStat')->success()->shouldBe(true);
        $this->checkHasProperty($fooObject, 'pubStat')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'pri')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'pro')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'pub')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'priStat')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'proStat')->success()->shouldBe(true);
        $this->checkHasProperty('FooCheckClass', 'pubStat')->success()->shouldBe(true);
        $this->checkHasProperty(new Exception(), 'message')->success()->shouldBe(true);
        $this->checkHasProperty((object) ['a' => 1, 'b' => 2], 'a')->success()->shouldBe(true);
        $this->checkHasProperty((object) ['a' => 1, 'b' => 2], 'b')->success()->shouldBe(true);

        $this->checkHasProperty($this, 'provides')->success()->shouldBe(false);
        $this->checkHasProperty('FooCheckClass', 'foo')->success()->shouldBe(false);
        $this->checkHasProperty('FooCheckClass', 'bar')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', 1]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', 1.0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkHasProperty', ['Iterator', curl_init()]);
    }
}
