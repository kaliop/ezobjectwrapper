<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Repository as eZRepository;

interface EntityInterface
{
    public function __construct(eZRepository $repository, Content $content=null, Location $location=null);

    /**
    * Return the content of the current location
    * @return \eZ\Publish\API\Repository\Values\Content\Content
    */
    public function content();

    /**
     * NB: if wrapper has been created from a Content, returns its MAIN location. Otherwise, the Location set at creation
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function location();
}
