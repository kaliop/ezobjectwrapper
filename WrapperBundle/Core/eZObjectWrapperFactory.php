<?php

namespace eZObject\WrapperBundle\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Factory which provide eZObjectWrapper objects or eZObjectWrapper children objects, according to parameters sets in eZObjectWrapper.yaml
 * Class eZObjectWrapperFactory
 * @package ezobject\WrapperBundle\Core
 */
class eZObjectWrapperFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * set the repository
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->repository = $this->container->get('ezpublish.api.repository');
    }

    /**
     * Create a eZObjectWrapper object, or a child class of eZObjectWrapper, according to parameters set in eZObjectWrapper.yml
     * @param $location integer|\eZ\Publish\API\Repository\Values\Content\Location
     * @return \eZObject\WrapperBundle\Core\eZObjectWrapper
     */
    public function buildeZObjectWrapper($location)
    {
        if(is_numeric($location)){
            $location = $this->repository->getLocationService()->loadLocation($location);
        }

        $contentTypeIdentifier = $this->repository->getContentTypeService()->loadContentType($location->contentInfo->contentTypeId)->identifier;
        $mappingEntities = $this->container->getParameter('class_mapping');

        if(isset($mappingEntities[$contentTypeIdentifier])){
            $className = $mappingEntities[$contentTypeIdentifier];
        } else {
            $className = 'ezobject\WrapperBundle\Core\eZObjectWrapper';
        }

        $objectWrapper = new $className($this->container, $location->id, $location);

        return $objectWrapper;
    }

}