<?php

namespace Kaliop\eZObjectWrapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TaggedServicesCompilerPass implements CompilerPassInterface
{
    protected $entityManagerService = 'ezobject_wrapper.entity_manager';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->entityManagerService)) {
            return;
        }
        $definition = $container->getDefinition($this->entityManagerService);
        $taggedServices = $container->findTaggedServiceIds('ezobject_wrapper.repository');
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