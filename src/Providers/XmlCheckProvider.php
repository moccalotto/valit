<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use Moccalotto\Exemel\Xml as XmlInspector;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Traits\ProvideViaReflection;
use SimpleXmlElement;

class XmlCheckProvider
{
    use ProvideViaReflection;

    protected function canParse($xmlString)
    {
        if (! is_string($xmlString)) {
            return false;
        }

        $prev = libxml_use_internal_errors(true);
        $xml = @simplexml_load_string($xmlString);
        libxml_use_internal_errors($prev);

        return $xml instanceof SimpleXmlElement;
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
     * @param string|SimpleXmlElement $value the value
     * @param string|SimpleXmlElement $against the value to check against
     * @param bool $skipWhite Ignore whitespace between tags.
     * @param bool $ignoreCase Ignore case differences in tags.
     *
     * @return Result
     */
    public function checkMatchesXmlAdvanced($value, $against, $skipWhite, $ignoreCase)
    {
        $msg = '{name} must match the given XML document';
        $context = compact('against', 'skipWhite', 'ignoreCase');

        if ($value instanceof SimpleXmlElement) {
            $valueXmlInspector = new XmlInspector($value);
        } elseif (is_string($value)) {
            if (! $this->canParse($value)) {
                return new Result(false, $msg, $context);
            }
            $valueXmlInspector = new XmlInspector(new SimpleXmlElement($value));
        } else {
            $valueXmlInspector = false;
        }

        $success = $valueXmlInspector && $valueXmlInspector->sameAs($against, $skipWhite, $ignoreCase);

        return new Result($success, $msg, $context);
    }

    /**
     * Check that $value matches $against, enforcing whitespace similarity as well.
     *
     * @param mixed $value
     * @param string|SimpleXmlElement $against
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
     * @param mixed $value
     * @param string|SimpleXmlElement $against
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
     * @param mixed $value
     * @param string|SimpleXmlElement $against
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
     * @param mixed $value
     * @param string|SimpleXmlElement $against
     * @return Result
     *
     * @Check("matchesXml")
     */
    public function checkMatchesXml($value, $against)
    {
        return $this->checkMatchesXmlAdvanced($value, $against, true, true);
    }
}
