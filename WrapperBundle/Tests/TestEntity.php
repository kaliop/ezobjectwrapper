<?php

namespace Test;

use Kaliop\eZObjectWrapperBundle\Core\Entity as BaseEntity;

class TestEntity extends BaseEntity
{
    public function getRepository()
    {
        return parent::getRepository();
    }

    public function getEntityManager()
    {
        return parent::getEntityManager();
    }
}