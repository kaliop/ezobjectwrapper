<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Kaliop\eZObjectWrapperBundle\Core\Exception\BadParameters;
use \eZ\Publish\API\Repository\Repository;

/**
 * A wrapper entity is an object with a location and its content, available via lazy loading.
 */
class eZObjectWrapper implements eZObjectWrapperInterface
{
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
     * @param Repository $repository
     * @throws BadParameters
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function initFromContent(Content $content)
    {
        $this->content = $content;
        $this->location = null;
        return $this;
    }

    public function initFromLocation(Location $location)
    {
        $this->content = null;
        $this->location = $location;
        return $this;
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
     * NB: if wrapper has been created from a Content, returns its MAIN location
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function location()
    {
        if($this->location == null){
            $this->location = $this->repository->getLocationService()->loadLocation($this->content->contentInfo->mainLocationId);
        }
        return $this->location;
    }
}
