<?php

include_once(__DIR__.'/BaseTest.php');
include_once(__DIR__ . '/../TestEntity.php');
include_once(__DIR__ . '/../TestRepository.php');

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class EntityManagerTest extends BaseTest
{
    public function testLoad()
    {
        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');

        $e2 = $entityManager->load($this->rootEntity->content());
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);

        $e2 = $entityManager->load($this->rootEntity->location());
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);

        $e2 = $entityManager->load($this->rootEntity->content()->contentInfo);
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);
    }

    public function testLoadMany()
    {
        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');

        $e2 = $entityManager->loadMany(array(
                $this->rootEntity->content(), $this->rootEntity->location(), $this->rootEntity->content()->contentInfo
        ));
        $this->assertCount(3, $e2);

        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(array(
            //new Criterion\ContentTypeIdentifier($this->contentTypeIdentifier),
            new Criterion\Subtree('/1/2/')
        ));
        //$query->performCount = false;
        $query->limit = 2;
        $query->offset = 0;
        $e2 = $entityManager->loadMany($this->container->get('ezpublish.api.repository')->getSearchService()->findContent($query));
        $this->assertGreaterThanOrEqual(1, count($e2));
    }

    public function testGetRepository()
    {
        $contentTypeService = $this->container->get('ezpublish.api.repository')->getContentTypeService();
        $contentTypeIdentifier = $contentTypeService->loadContentType($this->rootEntity->content()->contentInfo->contentTypeId)->identifier;

        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
        $repo = $entityManager->getRepository($contentTypeIdentifier);
        $this->assertEquals('Kaliop\eZObjectWrapperBundle\Repository\Base', get_class($repo));

        $repo = $entityManager->getRepositoryByContentTypeId($this->rootEntity->content()->contentInfo->contentTypeId);
        $this->assertEquals('Kaliop\eZObjectWrapperBundle\Repository\Base', get_class($repo));
    }

    public function testRegisterClass()
    {
        $contentTypeService = $this->container->get('ezpublish.api.repository')->getContentTypeService();
        $contentTypeIdentifier = $contentTypeService->loadContentType($this->rootEntity->content()->contentInfo->contentTypeId)->identifier;

        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
        $entityManager->registerClass('Test\TestRepository', $contentTypeIdentifier);

        $repo = $entityManager->getRepository($contentTypeIdentifier);
        $this->assertEquals('Test\TestRepository', get_class($repo));

        $repo = $entityManager->getRepositoryByContentTypeId($this->rootEntity->content()->contentInfo->contentTypeId);
        $this->assertEquals('Test\TestRepository', get_class($repo));

        $e2 = $entityManager->find($contentTypeIdentifier, $this->rootEntity->content()->id);
        $this->assertEquals('Test\TestEntity', get_class($e2));
    }

    public function testRegisterDefaultClass()
    {
        $contentTypeService = $this->container->get('ezpublish.api.repository')->getContentTypeService();
        $contentTypeIdentifier = $contentTypeService->loadContentType($this->rootEntity->content()->contentInfo->contentTypeId)->identifier;

        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');

        $repo = $entityManager->getRepository($contentTypeIdentifier);
        $this->assertEquals('Kaliop\eZObjectWrapperBundle\Repository\Base', get_class($repo));

        $entityManager->registerDefaultClass('Test\TestRepository');

        $repo = $entityManager->getRepository($contentTypeIdentifier);
        $this->assertEquals('Test\TestRepository', get_class($repo));
    }
}
