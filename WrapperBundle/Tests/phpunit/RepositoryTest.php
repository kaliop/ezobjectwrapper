<?php

include_once(__DIR__.'/BaseTest.php');

class RepositoryTest extends BaseTest
{
    public function testLoadByLocationId()
    {
        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
        $repo = $entityManager->getRepository('any');

        $e2 = $repo->loadEntityFromLocationId($this->rootEntity->location()->id);
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);
    }

    public function testLoadByRemoteId()
    {
        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');
        $repo = $entityManager->getRepository('any');

        $e2 = $repo->loadEntityFromContentRemoteId($this->rootEntity->content()->contentInfo->remoteId);
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);

        $e2 = $repo->loadEntityFromLocationRemoteId($this->rootEntity->location()->remoteId);
        $this->assertEquals($this->rootEntity->content()->id, $e2->content()->id);
    }
}
