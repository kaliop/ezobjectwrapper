<?php

include_once(__DIR__ . '/../TestEntity.php');
include_once(__DIR__ . '/../TestRepository.php');
include_once(__DIR__ . '/../TestTraitsRepository.php');

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    protected $rootEntity;
    protected $container;

    protected function setUp()
    {
        $this->container = $this->getContainer();
        $entityManager = $this->container->get('ezobject_wrapper.entity_manager');

        $rootContentId = $this->container->get('ezpublish.api.repository')->getLocationService()->loadLocation(2)->contentInfo->id;
        $this->rootEntity = $entityManager->find('any', $rootContentId);
    }

    protected function getContainer()
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }
        // run in our own test environment. Sf by default uses the 'test' one. We let phpunit.xml set it...
        $options = array(
            'environment' => $_SERVER['SYMFONY_ENV']
        );
        static::$kernel = static::createKernel($options);
        static::$kernel->boot();
        return static::$kernel->getContainer();
    }
}
