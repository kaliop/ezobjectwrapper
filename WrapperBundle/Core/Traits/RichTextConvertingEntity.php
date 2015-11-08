<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use eZ\Publish\Core\FieldType\XmlText\Converter;

/**
 * Adds the capability to generate html version of RichText fields
 */
trait RichTextConvertingEntity
{
    protected $richTextConverter;

    public function setRichTextConverter(Converter $richTextConverter)
    {
        $this->richTextConverter = $richTextConverter;
        return $this; // fluent interfaces for setters
    }

    protected function getHtml($xml)
    {
        return $this->richTextConverter->convert($xml);
    }
}
