<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use SimpleXMLElement;
use BadMethodCallException;
use Valit\Result\AssertionResult as Result;
use Moccalotto\Exemel\Xml as XmlInspector;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class XmlCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    protected function canParse($xmlString)
    {
        if (!is_string($xmlString)) {
            return false;
        }

        $prev = libxml_use_internal_errors(true);
        $xml = @simplexml_load_string($xmlString);
        libxml_use_internal_errors($prev);

        return is_a($xml, SimpleXMLElement::class);
    }

    /**
     * Check if $value is a string containing valid xml.
     *
     * @Check(["isValidXml", "validXml"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkXmlString($value)
    {
        return new Result(
            $this->canParse($value),
            '{name} must be a valid xml document'
        );
    }

    /**
     * Check that $value and $against are semantically the same.
     *
     * @Check("matchesXmlAdvanced")
     *
     * @param string|SimpleXMLElement $value      the value
     * @param string|SimpleXMLElement $against    the value to check against
     * @param bool                    $skipWhite  ignore whitespace between tags
     * @param bool                    $ignoreCase ignore case differences in tags
     *
     * @return Result
     */
    public function checkMatchesXmlAdvanced($value, $against, $skipWhite, $ignoreCase)
    {
        if (defined('HHVM_VERSION')) {
            throw new BadMethodCallException('XML validation is not implemented in HHVM');
        }
        if (!class_exists(XmlInspector::class)) {
            throw new BadMethodCallException(
                'XML validation needs the excemel library. Run `composer require moccalotto/exemel` to install'
            );
        }

        $message = '{name} must match the given XML document';
        $context = compact('against', 'skipWhite', 'ignoreCase');

        $valueXmlInspector = null;

        if (is_a($value, SimpleXMLElement::class)) {
            $valueXmlInspector = new XmlInspector($value);

            $success = $valueXmlInspector->sameAs($against, $skipWhite, $ignoreCase);

            return new Result($success, $message, $context);
        }

        if (!is_string($value)) {
            return new Result(false, $message, $context);
        }

        if (!$this->canParse($value)) {
            return new Result(false, $message, $context);
        }

        $valueXmlInspector = new XmlInspector(new SimpleXMLElement($value));

        $success = $valueXmlInspector->sameAs($against, $skipWhite, $ignoreCase);

        return new Result($success, $message, $context);
    }

    /**
     * Check that $value matches $against, enforcing whitespace similarity as well.
     *
     * @param mixed                   $value
     * @param string|SimpleXMLElement $against
     *
     * @return Result
     *
     * @Check("matchesXmlWithWhiteSpace")
     */
    public function checkMatchesXmlWithWhiteSpace($value, $against)
    {
        return $this->checkMatchesXmlAdvanced($value, $against, false, true);
    }

    /**
     * Check that $value matches $against, enforcing case similarity as well.
     *
     * @param mixed                   $value
     * @param string|SimpleXMLElement $against
     *
     * @return Result
     *
     * @Check("matchesXmlWithCase")
     */
    public function checkMatchesXmlWithCase($value, $against)
    {
        return $this->checkMatchesXmlAdvanced($value, $against, true, false);
    }

    /**
     * Check that $value matches $against, enforcing whitespace and case similarities as well.
     *
     * @param mixed                   $value
     * @param string|SimpleXMLElement $against
     *
     * @return Result
     *
     * @Check(["matchesXmlWithWhiteSpaceAndCase", "matchesXmlStrict"])
     */
    public function checkMatchesXmlStrict($value, $against)
    {
        return $this->checkMatchesXmlAdvanced($value, $against, false, false);
    }

    /**
     * Check that $value matches $against, ignoring differences in case and whitespace.
     *
     * @param mixed                   $value
     * @param string|SimpleXMLElement $against
     *
     * @return Result
     *
     * @Check("matchesXml")
     */
    public function checkMatchesXml($value, $against)
    {
        return $this->checkMatchesXmlAdvanced($value, $against, true, true);
    }
}
