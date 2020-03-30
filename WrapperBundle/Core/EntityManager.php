<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * A simple entity manager, inspired by the Doctrine one
 *
 * @todo add methods to automatically create an Entity of the good type given a Content, Location, Id or RemoteId ?
 */
class EntityManager
{
    /**
     * @var \eZ\Publish\API\Repository\Repository $repository
     */
    protected $repository;
    protected $classMap;
    protected $serviceMap;
    protected $defaultClass;
    protected $contentTypeIdentifierCache = array();

    /**
     * @param eZRepository $repository
     * @param array $classMap array of classes exposing a RepositoryInterface
     * @param RepositoryManagerInterface[] $serviceMap array of services exposing a RepositoryInterface
     */
    public function __construct(eZRepository $repository, array $classMap=array(), array $serviceMap=array())
    {
        $this->repository = $repository;
        foreach ($classMap as $contentTypeIdentifier => $className) {
            $this->registerClass($className, $contentTypeIdentifier);
        }
        foreach ($serviceMap as $contentTypeIdentifier => $service) {
            $this->registerService($service, $contentTypeIdentifier);
        }
    }

    /**
     * Registers an existing service to be used as repository for a given content type
     * @var RepositoryInterface $service
     * @var string $contentTypeIdentifier when null, we will ask the Repository to see if it already has its own $contentTypeIdentifier set up
     */
    public function registerService(RepositoryInterface $service, $contentTypeIdentifier)
    {
        if ($contentTypeIdentifier == null && is_callable(array($service, 'getContentTypeIdentifier'))) {
            $contentTypeIdentifier = $service->getContentTypeIdentifier();
        }
        if ($contentTypeIdentifier == null) {
            throw new \InvalidArgumentException("Service can not be registered as repository for NULL Content Type Identifier");
        }
        $this->serviceMap[$contentTypeIdentifier] = $service;
    }

    /**
     * Registers a php class to be used as wrapper for a given content type
     * @var string $className
     * @var string $contentTypeIdentifier
     * @throws \InvalidArgumentException
     *
     * @todo improve validation of contentTypeIdentifiers (is '0' a valid content type identifier?...)
     */
    public function registerClass($className, $contentTypeIdentifier)
    {
        if (!is_subclass_of($className, '\Kaliop\eZObjectWrapperBundle\Core\RepositoryInterface')) {
            throw new \InvalidArgumentException("Class '$className' can not be registered as repository because it lacks the necessary interface");
        }
        if ($contentTypeIdentifier == null) {
            throw new \InvalidArgumentException("Class '$className' can not be registered as repository for NULL Content Type Identifier");
        }
        $this->classMap[$contentTypeIdentifier] = $className;
    }

    /**
     * Registers a php class to be used as default repository
     * @var string $className
     * @throws \InvalidArgumentException
     */
    public function registerDefaultClass($className)
    {
        if (!is_subclass_of($className, '\Kaliop\eZObjectWrapperBundle\Core\RepositoryInterface')) {
            throw new \InvalidArgumentException("Class '$className' can not be registered as default repository because it lacks the necessary interface");
        }
        $this->defaultClass = $className;
    }

    /**
     * @param string $contentTypeIdentifier as used in the mapping
     * @return \Kaliop\eZObjectWrapperBundle\Core\RepositoryInterface
     * @throws \UnexpectedValueException
     */
    public function getRepository($contentTypeIdentifier)
    {
        if (isset($this->serviceMap[$contentTypeIdentifier])) {
            $repo = $this->serviceMap[$contentTypeIdentifier];
            return $repo->setContentTypeIdentifier($contentTypeIdentifier);
        }

        /// @todo for a small perf gain, we might store the created repo classes in an array
        if (isset($this->classMap[$contentTypeIdentifier])) {
            $repoClass = $this->classMap[$contentTypeIdentifier];
            $repo = new $repoClass($this->repository, $this);
            return $repo->setContentTypeIdentifier($contentTypeIdentifier);
        }

        if ($this->defaultClass != '') {
            $repoClass = $this->defaultClass;
            $repo = new $repoClass($this->repository, $this);
            return $repo->setContentTypeIdentifier($contentTypeIdentifier);
        }

        throw new \UnexpectedValueException("Content type '$contentTypeIdentifier' is not registered with the Entity Manager, can not retrieve a Repository for it");
    }

    /**
     * @param int $contentTypeId slightly slower than loading by Identifier, but is useful when used eg. by Entities
     * @return \Kaliop\eZObjectWrapperBundle\Core\RepositoryInterface
     * @throws \UnexpectedValueException
     */
    public function getRepositoryByContentTypeId($contentTypeId)
    {
        return $this->getRepository($this->getContentTypeIdentifierFromId($contentTypeId));
    }

    /**
     * A method added to keep the API friendly to Doctrine users
     * @param string $contentTypeIdentifier
     * @param mixed $id Content Id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function find($contentTypeIdentifier, $id)
    {
        return $this->getRepository($contentTypeIdentifier)->find($id);
    }

    /**
     * NB: this is slightly slower in execution than using find(), as it does have to look up the content type identifier.
     *
     * @param Location|Content|ContentInfo $content If you have a Content, by all means pass it in, not just its contentInfo
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If a content type with the given id and status DEFINED can not be found
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function load($content)
    {
        switch(true)
        {
            case $content instanceof ContentInfo:
                return $this
                    ->getRepositoryByContentTypeId($content->contentTypeId)
                    ->loadEntityFromContentInfo($content);
            case $content instanceof Content:
                return $this
                    ->getRepositoryByContentTypeId($content->contentInfo->contentTypeId)
                    ->loadEntityFromContent($content);
            case $content instanceof Location:
                return $this
                    ->getRepositoryByContentTypeId($content->contentInfo->contentTypeId)
                    ->loadEntityFromLocation($content);
        }
        throw new \UnexpectedValueException("Can not load an Entity for php object of class " . get_class($content));
    }

    /**
     * @param Content[]|Location[]|Contentinfo[]|SearchResult $contents
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface[] they keys of the $contents array get preserved
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If a content type with the given id and status DEFINED can not be found
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function loadMany($contents)
    {
        switch(true)
        {
            case $contents instanceof SearchResult:
                $entities = array();
                foreach ($contents->searchHits as $searchHit) {
                    $entities[] = $this->load($searchHit->valueObject);
                }
                return $entities;
            case is_array($contents):
                $entities = array();
                foreach ($contents as $key => $value) {
                    $entities[$key] = $this->load($value);
                }
                return $entities;
        }
        throw new \UnexpectedValueException("Can not load an Entities for php object of class " . get_class($contents));
    }

    /**
     * @param mixed $id
     * @return string
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If a content type with the given id and status DEFINED can not be found
     */
    protected function getContentTypeIdentifierFromId($id)
    {
        if (!isset($this->contentTypeIdentifierCache[$id])) {
            $contentTypeService = $this->repository->getContentTypeService();
            $this->contentTypeIdentifierCache[$id] = $contentTypeService->loadContentType($id)->identifier;
        }
        return $this->contentTypeIdentifierCache[$id];
    }
}
