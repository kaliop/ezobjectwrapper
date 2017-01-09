<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Adds the capability to generate links
 */
trait UrlGeneratingEntity
{
    protected $router;

    public function setRouter(UrlGeneratorInterface $router)
    {
        $this->router = $router;
        return $this; // fluent interfaces for setters
    }

    protected function getUrlAlias($absolute = false)
    {
        return $this->router->generate($this->location(), array(), $absolute);
    }
}
