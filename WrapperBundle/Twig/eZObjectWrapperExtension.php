<?php

namespace Kaliop\eZObjectWrapperBundle\Twig;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\View\ViewManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class eZObjectWrapperExtension extends AbstractExtension
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ViewManagerInterface
     */
    private $viewManager;

    /**
     * @param Repository $repository
     * @param ViewManagerInterface $viewManager
     */
    public function __construct(Repository $repository, ViewManagerInterface $viewManager)
    {
        $this->repository = $repository;
        $this->viewManager = $viewManager;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('render_location', array($this,'renderLocation'), array('is_safe' => array('html'))),
            new TwigFunction('render_content', array($this,'renderContent'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render a location according to the viewType and the ContentType set in override.yml, without doing a sub-request.
     *
     * @param int $locationID
     * @param string $viewType
     * @param array $params
     *
     * @return string generally it is safe html and does not need to be further encoded/escaped
     */
    public function renderLocation($locationID, $viewType, $params = array())
    {
        return $this->viewManager->renderLocation(
            $this->repository->getLocationService()->loadLocation($locationID),
            $viewType,
            $params
        );
    }

    /**
     * Render a content according to the viewType and the ContentType set in override.yml, without doing a sub-request.
     *
     * @param int $contentID
     * @param string $viewType
     * @param array $params
     *
     * @return string generally it is safe html and does not need to be further encoded/escaped
     */
    public function renderContent($contentID, $viewType, $params = array())
    {
        return $this->viewManager->renderContent(
            $this->repository->getContentService()->loadContent($contentID),
            $viewType,
            $params
        );
    }
}