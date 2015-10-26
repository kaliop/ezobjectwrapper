<?php

namespace Kaliop\eZObjectWrapperBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Kaliop\eZObjectWrapperBundle\DependencyInjection\KaliopeZObjectWrapperExtension;
use Kaliop\eZObjectWrapperBundle\DependencyInjection\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KaliopeZObjectWrapperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TaggedServicesCompilerPass());
    }

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
