<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait RouterInjectingRepository
{
    protected $router;

    public function setRouter(UrlGeneratorInterface $router)
    {
        $this->router = $router;
        return $this; // fluent interfaces for setters
    }

    protected function enrichEntityAtLoad($entity)
    {
        return $entity->setRouter($this->router);
    }
}
