<?php

namespace Kaliop\eZObjectWrapperBundle\Twig;

class eZObjectWrapperExtension extends \Twig_Extension
{
    private $viewController;

    public function __construct($viewController)
    {
        $this->viewController = $viewController;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('renderLocation', array($this,'render_location'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render a location according to the viewType and the ContentType set in override.yml, without doing a sub-request
     *
     * @param integer $locationID
     * @param string $viewType
     * @param array $params
     * @return string generally it is safe html and does not need to be further encoded/escaped
     */
    public function renderLocation($locationID, $viewType, $params = array())
    {
        $rendering = $this->viewController->viewLocation($locationID, $viewType, false, $params);

        return $rendering->getContent();
    }

    public function getName() {
        return 'ezobject_wrapper_extension';
    }
}
