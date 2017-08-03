<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Psr\Log\LoggerInterface;

/**
 * Typehint methods we expose from Logger and Repository using magic calls. NB: to be adjusted manually if those change...
 *
 * @method null emergency($message, array $context = array())
 * @method null alert($message, array $context = array())
 * @method null critical($message, array $context = array())
 * @method null error($message, array $context = array())
 * @method null warning($message, array $context = array())
 * @method null notice($message, array $context = array())
 * @method null info($message, array $context = array())
 * @method null debug($message, array $context = array())
 * @method null log($message, array $context = array())
 *
 * @method \eZ\Publish\API\Repository\ContentService getContentService()
 * @method \eZ\Publish\API\Repository\LanguageService getContentLanguageService()
 * @method \eZ\Publish\API\Repository\ContentTypeService getContentTypeService()
 * @method \eZ\Publish\API\Repository\LocationService getLocationService()
 * @method \eZ\Publish\API\Repository\TrashService getTrashService()
 * @method \eZ\Publish\API\Repository\SectionService getSectionService()
 * @method \eZ\Publish\API\Repository\SearchService getSearchService()
 * @method \eZ\Publish\API\Repository\UserService getUserService()
 * @method \eZ\Publish\API\Repository\URLAliasService getURLAliasService()
 * @method \eZ\Publish\API\Repository\URLWildcardService getURLWildcardService()
 * @method \eZ\Publish\API\Repository\ObjectStateService getObjectStateService()
 * @method \eZ\Publish\API\Repository\RoleService getRoleService()
 * @method \eZ\Publish\API\Repository\FieldTypeService getFieldTypeService()
 *
 * @method \eZ\Publish\API\Repository\Values\User\User getCurrentUser()
 * @method null setCurrentUser(\eZ\Publish\API\Repository\Values\User\User $user)
 * @method array|null hasAccess($module, $function, \eZ\Publish\API\Repository\Values\User\User $user = null)
 * @method bool canUser($module, $function, \eZ\Publish\API\Repository\Values\ValueObject $object, $targets = null)
 *
 * @method null beginTransaction()
 * @method null commit()
 * @method null rollback()
 *
 * @todo we could add simple methods like findAll() and fetch($offset, $limit) which simply filter based on contentType
 */
class Repository implements RepositoryInterface
{
    // Name of the php class used to create entities. Subclasses have to set a value to this, to be able to create entities
    protected $entityClass;

    protected $repository;
    protected $entityManager;
    protected $contentTypeIdentifier;
    protected $settings;
    protected $logger;

    /**
     * Prioritized languages
     *
     * @var array
     */
    protected $languages;

    public function __construct(eZRepository $repository, $entityManager, array $settings=array(), $contentTypeIdentifier='')
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->settings = $this->validateSettings($settings);
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    public function setContentTypeIdentifier($contentTypeIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        return $this;
    }

    public function setSettings(array $settings)
    {
        $this->settings = $this->validateSettings($settings);
        return $this;
    }

    public function setLogger(LoggerInterface $logger=null)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setLanguages(array $languages = null)
    {
        $this->languages = $languages;
    }

    /**
     * Called from the constructor, with the settings received from the caller.
     * Subclasses can implement checking here, or merge the received settings with other data, using f.e. the Symfony
     * OptionsResolver component (see http://symfony.com/doc/current/components/options_resolver.html).
     *
     * @param array $settings
     * @return array
     */
    protected function validateSettings(array $settings)
    {
        return $settings;
    }

    /**
     * Nice syntactic sugar ( manually typehinted :-) )
     * Allow all logger methods and eZ repo methods to be called on this extended repo
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @todo !important move this method to protected access?
     */
    public function __call($method, $args)
    {
        switch($method) {
            case 'emergency':
            case 'alert':
            case 'critical':
            case 'error':
            case 'warning':
            case 'notice':
            case 'info':
            case 'debug':
            case 'log':
                if ($this->logger) {
                    return call_user_func_array(array($this->logger, $method), $args);
                }
                // if no logger is defined, swallow the method call
                return;
            default:
                return call_user_func_array(array($this->repository, $method), $args);
        }
    }

    /**
     * To be overridden in subclasses, this method allows injecting extra services/settings in the entities created.
     * This version 'knows' about EntityManagerAware and Logging entity traits.
     *
     * @param \Kaliop\eZObjectWrapperBundle\Core\EntityInterface $entity
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    protected function enrichEntityAtLoad($entity)
    {
        if (is_callable(array($entity, 'setLogger'))) {
            $entity->setLogger($this->logger);
        }
        if (is_callable(array($entity, 'setEntityManager'))) {
            $entity->setEntityManager($this->entityManager);
        }
        if (is_callable(array($entity, 'setLanguages'))) {
            $entity->setLanguages($this->languages);
        }
        return $entity;
    }

    /**
     * @param Content $content
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     *
     * @todo optionally (?) throw an error if bad content type is detected
     */
    public function loadEntityFromContent(Content $content)
    {
        $class = $this->entityClass;
        $entity = new $class($this->repository, $content, null);
        return $this->enrichEntityAtLoad($entity);
    }

    /**
     * @param Location $location
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    public function loadEntityFromLocation(Location $location)
    {
        $class = $this->entityClass;
        $entity = new $class($this->repository, null, $location);
        return $this->enrichEntityAtLoad($entity);
    }

    /**
     * @param ContentInfo $contentInfo
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    public function loadEntityFromContentInfo(ContentInfo $contentInfo)
    {
        return $this->loadEntityFromContent($this->getContentService()->loadContentByContentInfo($contentInfo));
    }

    /**
     * @param int $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function loadEntityFromContentId($id)
    {
        return $this->loadEntityFromContent($this->getContentService()->loadContent($id));
    }

    /**
     * An alias for loadEntityFromContentId, to keep the API close to Doctrine
     * @param int $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function find($id)
    {
        return $this->loadEntityFromContentId($id);
    }

    /**
     * @param int $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If the specified location is not found
     */
    public function loadEntityFromLocationId($id)
    {
        return $this->loadEntityFromLocation($this->getLocationService()->loadLocation($id));
    }

    /**
     * @param string $remoteId
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function loadEntityFromContentRemoteId($remoteId)
    {
        return $this->loadEntityFromContent($this->getContentService()->loadContentByRemoteId($remoteId));
    }

    /**
     * @param string $remoteId
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If the specified location is not found
     */
    public function loadEntityFromLocationRemoteId($remoteId)
    {
        return $this->loadEntityFromLocation($this->getLocationService()->loadLocationByRemoteId($remoteId));
    }

    /**
     * NB: assumes that all search results are homogeneous (same content type)
     * @param SearchResult $searchResult
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface[]
     */
    protected function loadEntitiesFromSearchResults(SearchResult $searchResult)
    {
        $entities = array();
        foreach ($searchResult->searchHits as $searchHit) {
            // let's hope that in the future eZPublish does not add new types of results to SearchResult... :-P
            if ($searchHit->valueObject instanceof \eZ\Publish\API\Repository\Values\Content\Location) {
                $entities[] = $this->loadEntityFromLocation($searchHit->valueObject);
            } else {
                $entities[] = $this->loadEntityFromContent($searchHit->valueObject);
            }
        }
        return $entities;
    }
}
