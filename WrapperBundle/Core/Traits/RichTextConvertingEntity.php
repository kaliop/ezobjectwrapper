<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use eZ\Publish\Core\FieldType\XmlText\Converter;

/**
 * Adds the capability to generate html version of RichText fields
 */
trait RichTextConvertingEntity
{
    protected $richTextConverter;

    /**
     * @param Converter $richTextConverter
     * @return $this
     */
    public function setRichTextConverter(Converter $richTextConverter)
    {
        $this->richTextConverter = $richTextConverter;
        return $this; // fluent interfaces for setters
    }

    /**
     * @param string $xml the value of an xml-text field
     * @return string
     */
    protected function getHtml($xml)
    {
        return $this->richTextConverter->convert($xml);
    }

    /**
     * @param $fieldName the identifier of an xml-text field
     * @return string
     */
    protected function getHtmlForField($fieldName)
    {
        return $this->getHtml($this->content()->getFieldValue($fieldName)->xml);
    }
}
