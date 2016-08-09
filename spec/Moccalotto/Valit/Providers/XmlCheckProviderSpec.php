<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit\Providers;

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

    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\XmlCheckProvider');
    }

    public function it_checks_xmlString()
    {
        $this->checkXmlString($this->xmlBase)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isValidXml');
        $this->provides()->shouldHaveKey('validXml');

        $this->checkXmlString('<root></root>')->success()->shouldBe(true);
        $this->checkXmlString($this->xmlBase)->success()->shouldBe(true);
        $this->checkXmlString($this->xmlMatches)->success()->shouldBe(true);

        $this->checkXmlString($this->xmlInvalid)->success()->shouldBe(false);
    }

    public function it_checks_matchesXmlAdvanced()
    {
        $this->checkMatchesXmlAdvanced('', '', true, true)->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_matchesXml()
    {
        $this->checkMatchesXml('', '')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('matchesXml');

        $this->checkMatchesXml(
            '<root></root>',
            '<ROOT>    </ROOT>'
        )->success()->shouldBe(true);
    }

    public function it_checks_MatchesXmlWithWhiteSpace()
    {
        $this->checkMatchesXmlWithWhiteSpace('', '')->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_MatchesXmlWithCase()
    {
        $this->checkMatchesXmlWithCase('', '')->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_MatchesXmlStrict()
    {
        $this->checkMatchesXmlStrict('', '')->shouldHaveType('Moccalotto\Valit\Result');

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
