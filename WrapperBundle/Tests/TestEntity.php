<?php

namespace Test;

use Kaliop\eZObjectWrapperBundle\Core\Entity as BaseEntity;

use Kaliop\eZObjectWrapperBundle\Core\Traits\RelationTraversingEntity;
use Kaliop\eZObjectWrapperBundle\Core\Traits\RichTextConvertingEntity;
use Kaliop\eZObjectWrapperBundle\Core\Traits\UrlGeneratingEntity;

class TestEntity extends BaseEntity
{
    use RelationTraversingEntity;
    use RichTextConvertingEntity;
    use UrlGeneratingEntity;

    public function getRepository()
    {
        return parent::getRepository();
    }

    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

    public function getHtmlFor($fieldName)
    {
        return $this->getHtml($this->content()->getFieldValue($fieldName)->xml);
    }

    public function getURL($absolute = false)
    {
        return $this->getURLAlias($absolute);
    }

    public function getRelationsFor($fieldName)
    {
        return $this->getRelations($fieldName);
    }

    public function getRelationFor($fieldName)
    {
        return $this->getRelation($fieldName);
    }
}
