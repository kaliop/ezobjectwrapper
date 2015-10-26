<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * A 'factory-like' service, used to create new instances of itself.
 * It can be subclassed to inject more dependencies into it, and registered in the service_map configuration.
 *
 * NB: since services are singletons, the initFromContent and initFromLocation locations create new object instances
 * each time. If you subclass this class, remember to reimplement those to be sure to properly initialize the created
 * instances with all the desired state.
 */
class eZObjectWrapperService extends eZObjectWrapper
{
    /**
     * @param $content
     * @param $location
     */
    protected function init(Content $content=null, Location $location=null)
    {
        $this->content = $content;
        $this->location = $location;
    }

    /**
     * @param Content $content
     * @return static
     */
    public function initFromContent(Content $content)
    {
        $wrapper = new static($this->repository);
        $wrapper->init($content, null);
        return $wrapper;
    }

    /**
     * @param Location $location
     * @return static
     */
    public function initFromLocation(Location $location)
    {
        $wrapper = new static($this->repository);
        $wrapper->init(null, $location);
        return $wrapper;
    }
}