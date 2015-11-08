<?php

namespace Test;

use Kaliop\eZObjectWrapperBundle\Core\Repository as BaseRepository;

class TestRepository extends BaseRepository
{
    protected $entityClass = '\Test\TestEntity';
}