<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use eZ\Publish\Core\FieldType\XmlText\Converter;

trait RichTextConverterInjectingRepository
{
    protected $richTextConverter;

    public function setRichTextConverter(Converter $richTextConverter)
    {
        $this->richTextConverter = $richTextConverter;
        return $this; // fluent interfaces for setters
    }

    protected function enrichEntityAtLoad($entity)
    {
        return $entity->setRichTextConverter($this->richTextConverter);
    }
}
