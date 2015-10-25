<?php

namespace Kaliop\eZObjectWrapperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ezobject_wrapper');

        $rootNode
            ->children()
                ->scalarNode('default_wrapper_class')->defaultValue('\Kaliop\eZObjectWrapperBundle\Core\eZObjectWrapper')->end()
                ->arrayNode('class_map')->isRequired()->prototype('scalar')->end()->end()
                ->arrayNode('service_map')->isRequired()->prototype('scalar')->end()->end()
            ->end();

        return $treeBuilder;
    }
}
