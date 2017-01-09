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
}