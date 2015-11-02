<?php

namespace Kaliop\eZObjectWrapperBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class eZObjectWrapperExtension extends \Twig_Extension
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_location', array($this,'renderLocation'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render a location according to the viewType and the ContentType set in override.yml, without doing a sub-request.
     * Note: we can NOT inject directly the 'ezpublish.controller.content.view' service because it generates a ServiceCircularReferenceException
     *
     * @param int $locationID
     * @param string $viewType
     * @param array $params
     * @return string generally it is safe html and does not need to be further encoded/escaped
     */
    public function renderLocation($locationID, $viewType, $params = array())
    {
        $rendering = $this->container->get('ezpublish.controller.content.view')
            ->viewLocation($locationID, $viewType, false, $params);

        return $rendering->getContent();
    }

    public function getName()
    {
        return 'ezobject_wrapper_extension';
    }
}