<?php

namespace Test;

use Kaliop\eZObjectWrapperBundle\Core\Repository as BaseRepository;
use Kaliop\eZObjectWrapperBundle\Core\Traits\RichTextConverterInjectingRepository;
use Kaliop\eZObjectWrapperBundle\Core\Traits\RouterInjectingRepository;

class TestTraitsRepository extends BaseRepository
{
    use RichTextConverterInjectingRepository;
    use RouterInjectingRepository;

    protected $entityClass = '\Test\TestEntity';

    protected function enrichEntityAtLoad($entity)
    {
        parent::enrichEntityAtLoad($entity);
        return $entity->setRichTextConverter($this->richTextConverter)->setRouter($this->router);
    }
}
