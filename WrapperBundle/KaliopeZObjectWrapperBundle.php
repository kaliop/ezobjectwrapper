<?php

namespace Kaliop\eZObjectWrapperBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Kaliop\eZObjectWrapperBundle\DependencyInjection\KaliopeZObjectWrapperExtension;
class KaliopeZObjectWrapperBundle extends Bundle
{
    /**
     * This is only needed to avoid Sf complaining that our extension has a custom alias
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new KaliopeZObjectWrapperExtension();
        }

        return $this->extension;
    }
}
