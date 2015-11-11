<?php

include_once(__DIR__.'/BaseTest.php');

class EntityTest extends BaseTest
{
    public function testGetRepository()
    {
        $contentTypeService = $this->container->get('ezpublish.api.repository')->getContentTypeService();
        $contentTypeIdentifier = $contentTypeService->loadContentType($this->rootEntity->content()->contentInfo->contentTypeId)->identifier;

        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
        $entityManager->registerClass('Test\TestRepository', $contentTypeIdentifier);

        $e2 = $entityManager->find($contentTypeIdentifier, $this->rootEntity->content()->id);

        $repo = $e2->getRepository();
        $this->assertEquals('Test\TestRepository', get_class($repo));
    }
}