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
                ->scalarNode('default_repository_class')
                    ->defaultValue('\Kaliop\eZObjectWrapperBundle\Repository\Base')
                    ->info('The default wrapper class to be used for all content types which have no explicit mapping')
                ->end()
                ->arrayNode('class_map')
                    ->defaultValue(array())
                    ->info('A list of class names used to implement wrappers for specific content types. Array key is the content type identifier')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
