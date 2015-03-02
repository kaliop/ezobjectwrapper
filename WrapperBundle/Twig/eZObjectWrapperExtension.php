<?php

namespace eZObject\WrapperBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\MVC\Symfony\Controller\Content\ViewController;
use eZ\Publish\Core\MVC\Symfony\View\Provider\Location\Configured;
use eZSys;


class eZObjectWrapperExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct($container = null) {
        if ($container) {
            $this->container  = $container;
            $this->repository = $this->container->get('ezpublish.api.repository');
        }
    }

    public function getFunctions() {
        return array(new \Twig_SimpleFunction('renderLocation', array($this,'renderLocation')),);
    }

    /**
     * Render a location according to the viewType and the ContentType set in override.yml
     * @param $locationID integer
     * @param $viewType string
     * @param null $params
     * @return string
     */
    public function renderLocation($locationID, $viewType, $params = array()) {
        $rendering = $this->container->get('ez_content')->viewLocation($locationID, $viewType, false, $params);

        return htmlspecialchars_decode($rendering->getContent());
    }


    public function getName() {
        return 'ezobject_wrapper_extension';
    }


}