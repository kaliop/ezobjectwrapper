<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use \eZ\Publish\API\Repository\Repository;

class eZObjectWrapperFactory
{

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;
    protected $classMap = array();
    protected $serviceMap = array();
    protected $defaultClass;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Registers an existing service to be used as wrapper for a given content type
     * @var $service
     * @var string $contentIdentifier
     */
    public function registerService($service, $contentIdentifier)
    {
        $this->serviceMap[$contentIdentifier] = $service;
    }

    /**
     * Registers a php class to be used as wrapper for a given content type
     * @var string $className
     * @var string $contentIdentifier
     */
    public function registerClass($className, $contentIdentifier)
    {
        $this->classMap[$contentIdentifier] = $className;
    }

    /**
     * Registers a php class to be used as default wrapper
     * @var string $className
     */
    public function registerDefaultClass($className)
    {
        $this->defaultClass = $className;
    }

    /**
     * Create an eZObjectWrapper object, or a child class of eZObjectWrapper, according to parameters set in eZObjectWrapper.yml
     * @param integer|Location|Content $source when integer, a Location Id is supposed
     * @return \Kaliop\eZObjectWrapperBundle\Core\eZObjectWrapperInterface
     * @throws \Exception and many others
     */
    public function build($source)
    {
        $locationSource = null;
        $contentSource = null;

        if (is_numeric($source)) {
            $source = $this->repository->getLocationService()->loadLocation($source);
        }
        if ($source instanceof Location) {
            $locationSource = $source;
        } elseif ($source instanceof Content) {
            $contentSource = $source;
        } else {
            throw new \Exception("Can not build an eZObjectWrapper out of a: " . gettype($source));
        }

        $contentTypeId = $source->contentInfo->contentTypeId;
        $contentTypeIdentifier = $this->repository->getContentTypeService()->loadContentType($contentTypeId)->identifier;

        if (isset($this->serviceMap[$contentTypeIdentifier])) {

            $objectWrapper = $this->serviceMap[$contentTypeIdentifier];

        } else {
            if (isset($this->classMap[$contentTypeIdentifier])) {
                $className = $this->classMap[$contentTypeIdentifier];
            } elseif ($this->defaultClass != '') {
                $className = $this->defaultClass ;
            } else {
                throw new \Exception("Can not build an eZObjectWrapper out of content: no mapped class available for content type $contentTypeId");
            }

            $objectWrapper = new $className($this->repository);
        }

        if ($locationSource !== null) {
            return $objectWrapper->initFromLocation($locationSource);
        } elseif ($source instanceof Content) {
            return $objectWrapper->initFromContent($contentSource);
        }
    }

    /**
     * Returns an array of eZObjectWrapperInterface objects
     * @param array $sources can be Content, Location or
     * @return eZObjectWrapperInterface[]
     */
    public function buildFromArray(array $sources)
    {
        $objectWrapperList = array();

        foreach ($sources as $id => $source) {
            $objectWrapperList[$id] = $this->build($source);
        }

        return $objectWrapperList;
    }

    /**
     * @param mixed $id
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromContentId($id)
    {
        return $this->build($this->repository->getContentService()->loadContent($id));
    }

    /**
     * @param mixed $remoteId
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromContentRemoteId($remoteId)
    {
        return $this->build($this->repository->getContentService()->loadContentByRemoteId($remoteId));
    }

    /**
     * @param mixed $remoteId
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromLocationRemoteId($remoteId)
    {
        return $this->build($this->repository->getLocationService()->loadLocationByRemoteId($remoteId));
    }
}
