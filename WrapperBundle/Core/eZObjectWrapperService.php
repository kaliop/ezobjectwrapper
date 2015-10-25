<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use \eZ\Publish\API\Repository\Repository;

/**
 * A 'factory-like' service, used to create new instances of itself.
 * It can be subclassed to inject more dependencies into it, and registered in the service_map configuration.
 * NB: since services are singletons, the initFromContent and initFromLocation locations create new object instances each time.
 */
class eZObjectWrapperService extends eZObjectWrapper
{
    /**
     * @param $content
     * @param $location
     */
    protected function init($content, $location)
    {
        $this->content = $content;
        $this->location = $location;
    }

    public function initFromContent(Content $content)
    {
        $wrapper = new static($this->repository);
        $wrapper->init($content, null);
        return $wrapper;
    }

    public function initFromLocation(Location $location)
    {
        $wrapper = new static($this->repository);
        $wrapper->init(null, $location);
        return $wrapper;
    }
}