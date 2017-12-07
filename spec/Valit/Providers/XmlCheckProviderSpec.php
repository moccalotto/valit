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

use PhpSpec\ObjectBehavior;

class XmlCheckProviderSpec extends ObjectBehavior
{
    protected $xmlBase = <<<XML
<?xml version="1.0"?>
<root>
    <!-- Some Comment -->
    <foo attr1="bar" attr2="boss">
        <baz>DING</baz>
    </foo>
</root>
XML;

    protected $xmlMatches = <<<XML
<?xml version="1.0"?>
<ROOT><foo      Attr2="boss"    attr1="bar">
    <baz>DING</baz>
<!-- case of tags and attributes is ignored -->
</foo></ROOT>
XML;

    protected $xmlMismatch = <<<XML
<?xml version="1.0"?>
<root>
    <foo attr1="bar" attr2="boss">
        <baz>ding</baz>
        <!-- ding should be DING -->
        <!-- only case of tags is ignored -->
    </foo>
</root>
XML;

    protected $xmlInvalid = <<<NOT_XML
<root>
    <floot/>
    <snoot/>
</scoot>
NOT_XML;

    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\XmlCheckProvider');
    }

    function it_checks_xmlString()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkXmlString($this->xmlBase)->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('isValidXml');
        $this->provides()->shouldHaveKey('validXml');

        $this->checkXmlString('<root></root>')->success()->shouldBe(true);
        $this->checkXmlString($this->xmlBase)->success()->shouldBe(true);
        $this->checkXmlString($this->xmlMatches)->success()->shouldBe(true);

        $this->checkXmlString($this->xmlInvalid)->success()->shouldBe(false);
    }

    function it_checks_matchesXmlAdvanced()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkMatchesXmlAdvanced('', '', true, true)->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('matchesXmlAdvanced');

        $this->checkMatchesXmlAdvanced($this->xmlBase, $this->xmlMatches, true, true)->success()->shouldBe(true);

        $this->checkMatchesXmlAdvanced($this->xmlBase, $this->xmlMatches, false, true)->success()->shouldBe(false);
        $this->checkMatchesXmlAdvanced($this->xmlBase, $this->xmlMatches, true, false)->success()->shouldBe(false);
        $this->checkMatchesXmlAdvanced($this->xmlBase, $this->xmlMismatch, true, true)->success()->shouldBe(false);
        $this->checkMatchesXmlAdvanced($this->xmlInvalid, $this->xmlMatches, true, true)->success()->shouldBe(false);

        $this->shouldThrow('Exception')->duringCheckMatchesXmlAdvanced(
            $this->xmlBase,
            $this->xmlInvalid,
            true,
            true
        );

        $this->provides()->shouldHaveKey('matchesXmlAdvanced');
    }

    function it_checks_matchesXml()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkMatchesXml('', '')->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('matchesXml');

        $this->checkMatchesXml(
            '<root></root>',
            '<ROOT>    </ROOT>'
        )->success()->shouldBe(true);
    }

    function it_checks_MatchesXmlWithWhiteSpace()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkMatchesXmlWithWhiteSpace('', '')->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('matchesXmlWithWhiteSpace');

        $this->checkMatchesXmlWithWhiteSpace(
            '<root></root>',
            '<ROOT></ROOT>'
        )->success()->shouldBe(true);

        $this->checkMatchesXmlWithWhiteSpace(
            '<root></root>',
            '<ROOT>    </ROOT>'
        )->success()->shouldBe(false);
    }

    function it_checks_MatchesXmlWithCase()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkMatchesXmlWithCase('', '')->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('matchesXmlWithCase');

        $this->checkMatchesXmlWithCase(
            '<root></root>',
            '<root>    </root>'
        )->success()->shouldBe(true);

        $this->checkMatchesXmlWithCase(
            '<root></root>',
            '<ROOT>    </ROOT>'
        )->success()->shouldBe(false);
    }

    function it_checks_MatchesXmlStrict()
    {
        if (defined('HHVM_VERSION')) {
            return;
        }

        $this->checkMatchesXmlStrict('', '')->shouldHaveType('Valit\Result');

        $this->provides()->shouldHaveKey('matchesXmlStrict');

        $this->checkMatchesXmlStrict(
            '<root></root>',
            '<root></root>'
        )->success()->shouldBe(true);

        $this->checkMatchesXmlStrict(
            '<root></root>',
            '<root>    </root>'
        )->success()->shouldBe(false);

        $this->checkMatchesXmlStrict(
            '<root></root>',
            '<ROOT></ROOT>'
        )->success()->shouldBe(false);
    }
}
