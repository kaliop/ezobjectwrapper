<?php

namespace eZObject\WrapperBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;


class eZObjectWrapperExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container) {

        $this->container = $container;

    }

    public function getFunctions() {
        return array(new \Twig_SimpleFunction('renderLocation', array($this,'renderLocation')),);
    }

    /**
     * Render a location according to the viewType and the ContentType set in override.yml
     * @param $locationID integer
     * @param $viewType string
     * @param array $params
     * @return string
     */
    public function renderLocation($locationID, $viewType, $params = array()) {
        $rendering = $this->container->get('ezpublish.controller.content.view')->viewLocation($locationID, $viewType, false, $params);

        return htmlspecialchars_decode($rendering->getContent());
    }


    public function getName() {
        return 'ezobject_wrapper_extension';
    }


}