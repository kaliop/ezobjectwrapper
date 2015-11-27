<?php

namespace Kaliop\eZObjectWrapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KaliopeZObjectWrapperExtension extends Extension
{
    protected $entityManagerService = 'ezobject_wrapper.entity_manager';

    public function getAlias()
    {
        return 'ezobject_wrapper';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->injectConfiguration($config, $container);
    }

    protected function injectConfiguration(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition($this->entityManagerService)) {
            $factoryDefinition = $container->findDefinition($this->entityManagerService);

            $factoryDefinition->addMethodCall('registerDefaultClass', array($config['default_repository_class']));

            foreach ($config['class_map'] as $type => $class) {
                $factoryDefinition
                    ->addMethodCall('registerClass', array($class, $type));
            }
        }
    }
}
