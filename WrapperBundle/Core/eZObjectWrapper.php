<?php

namespace ezobject\WrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An entity is an object with a location and his content, available in lazy loading.
 * Class kEntity
 * @package ezobject\WrapperBundle\Core
 */
class eZObjectWrapper implements eZObjectWrapperInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var integer
     */
    public $locationId;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    public $location;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content = null;


    /**
     * Set the repository
     * @param ContainerInterface $container
     * @param $locationId
     * @param $location
     */
    public function __construct(ContainerInterface $container, $locationId, $location)
    {
        $this->container = $container;
        $this->repository = $this->container->get('ezpublish.api.repository');

        $this->locationId = $locationId;
        $this->location = $location;
    }

    /**
     * Load the content for the object's location ID if the content is not yet loaded (Lazy Loading)
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function content()
    {
        if($this->content == null){
            $this->content = $this->repository->getContentService()->loadContent($this->location->contentId);
        }
        return $this->content;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set the wrapper's content
     * @param  \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}

