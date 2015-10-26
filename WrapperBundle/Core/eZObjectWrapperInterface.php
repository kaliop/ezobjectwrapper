<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;

interface eZObjectWrapperInterface
{
    /**
     * Provide access to the Content
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function content();

    /**
     * Provide access to the (main if needed) Location
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function location();

    /**
     * @param Content $content
     * @return eZObjectWrapperInterface
     */
    public function initFromContent(Content $content);

    /**
     * @param Location $location
     * @return eZObjectWrapperInterface
     */
    public function initFromLocation(Location $location);
}
