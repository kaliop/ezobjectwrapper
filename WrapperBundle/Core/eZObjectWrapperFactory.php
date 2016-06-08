<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Foncia\eZObjectWrapper\HelpBlock;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;


/**
 * Factory which provide eZObjectWrapper objects or eZObjectWrapper children objects, according to parameters sets in eZObjectWrapper.yaml
 * Class eZObjectWrapperFactory
 * @package Kaliop\eZObjectWrapperBundle\Core
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
     * @param $source integer|Location|Content
     * @return \Kaliop\eZObjectWrapperBundle\Core\eZObjectWrapper
     */
    public function buildeZObjectWrapper($source)
    {
        $locationSource = null;
        $contentSource = null;

        if (is_numeric($source)) {
            try {
                $locationSource = $source = $this->repository->getLocationService()->loadLocation($source);
            } catch (NotFoundException $e) {
                return false;
            }
        } elseif ($source instanceof Content) {
            $contentSource = $source;
        } else {
            $locationSource = $source;
        }

        $contentTypeIdentifier = $this->repository->getContentTypeService()->loadContentType($source->contentInfo->contentTypeId)->identifier;
        $mappingEntities = $this->container->getParameter('class_mapping');
        $defaultClass = $this->container->getParameter('default_ezobject_class');

        if(isset($mappingEntities[$contentTypeIdentifier])){
            $className = $mappingEntities[$contentTypeIdentifier];
        } else {
            $className = $defaultClass;
        }

        $objectWrapper = new $className($this->container, $locationSource, $contentSource);

        return $objectWrapper;
    }

    /**
     * Returns an array of eZObjectWrapper objects - wrapper's location is the content's main location
     * @param array $contentIds
     * @return array
     */
    public function buildeZObjectWrappersByContentIds(array $contentIds)
    {
        $objectWrapperList = array();

        foreach ($contentIds as $contentId) {
            $content = $this->repository->getContentService()->loadContent($contentId);
            $contentInfo = $content->contentInfo;
            if ($contentInfo->mainLocationId !== null) {
                $location = $this->repository->getLocationService()->loadLocation($contentInfo->mainLocationId);
                $objectWrapper = $this->buildeZObjectWrapper($location);
                $objectWrapper->setContent($content);
                $objectWrapperList[]=$objectWrapper;
            }
        }

        return $objectWrapperList;
    }

}
