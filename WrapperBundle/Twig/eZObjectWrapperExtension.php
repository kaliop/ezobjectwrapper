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
     * @var ViewController
     */
   private $viewController;

    public function __construct(\eZ\Publish\Core\MVC\Symfony\Controller\Content\ViewController $viewController) {

      $this->viewController = $viewController;

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
        $rendering = $this->viewController->viewLocation($locationID, $viewType, false, $params);

        return htmlspecialchars_decode($rendering->getContent());
    }


    public function getName() {
        return 'ezobject_wrapper_extension';
    }


}