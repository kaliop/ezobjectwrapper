<?php

namespace eZObject\WrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZObject\WrapperBundle\Core\Exception\BadParameters;
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
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $location = null;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content = null;


    /**
     * Set the repository
     * @param ContainerInterface $container
     * @param Location $location
     * @param Content $content
     * @throws BadParameters
     */
    public function __construct(ContainerInterface $container, $location = null, $content = null)
    {
        if($location == null && $content ==  null) {
            throw new BadParameters();
        }

        $this->container = $container;
        $this->repository = $this->container->get('ezpublish.api.repository');

        $this->location = $location;
        $this->content = $content;
    }

    /**
     * Return the content of the current location
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
     * Return the MAIN location of the current content
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function location()
    {
        if($this->location == null){
            $this->location = $this->repository->getLocationService()->loadLocation($this->content->contentInfo->mainLocationId);
        }
        return $this->location;
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

