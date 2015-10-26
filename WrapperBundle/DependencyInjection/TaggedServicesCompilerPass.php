<?php

namespace Kaliop\eZObjectWrapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TaggedServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezobject_wrapper.factory')) {
            return;
        }

        $definition = $container->getDefinition(
            'ezobject_wrapper.factory'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'ezobject_wrapper.wrapper'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'registerService',
                    array(new Reference($id), $attributes["content_type"])
                );
            }
        }
    }
}
