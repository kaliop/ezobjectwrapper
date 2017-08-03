<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Repository as eZRepository;
use Kaliop\eZObjectWrapperBundle\Core\Traits\LoggingEntity;
use Kaliop\eZObjectWrapperBundle\Core\Traits\EntityManagerAwareEntity;

class Entity implements EntityInterface
{
    use LoggingEntity;
    use EntityManagerAwareEntity;

    /**
     * @var \eZ\Publish\API\Repository\Repository $repository
     */
    protected $repository;
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    protected $location;
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    protected $content;

    protected $settings;

    /**
     * Prioritized languages
     *
     * @var array
     */
    protected $languages;

    /**
     * @param eZRepository $repository
     * @param Content $content
     * @param Location $location
     * @param array $settings used to pass to the Entity instance anything that would be done via DIC if it was a service
     * @throws \InvalidArgumentException
     */
    public function __construct(eZRepository $repository, Content $content=null, Location $location=null, array $settings = array())
    {
        if ($content == null && $location == null) {
            throw new \InvalidArgumentException('Trying to create Entity with no content or location');
        }
        if ($content != null && $location != null) {
            if ($location->contentId != $content->id)
                throw new \InvalidArgumentException('Trying to create Entity with mismatching content and location');
        }
        $this->content = $content;
        $this->location = $location;
        $this->repository = $repository;
        $this->settings = $this->validateSettings($settings);
    }

    public function setLanguages(array $languages = null)
    {
        $this->languages = $languages;
    }

    /**
     * Called from the constructor, with the settings received from the caller.
     * Subclasses can implement checking here, or merge the received settings with other data, using f.e. the Symfony
     * OptionsResolver component (see http://symfony.com/doc/current/components/options_resolver.html).
     *
     * @param array $settings
     * @return array
     */
    protected function validateSettings(array $settings)
    {
        return $settings;
    }

    /**
     * Return the content of the current location
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function content()
    {
        if($this->content == null){
            $this->content = $this->repository->getContentService()->loadContent($this->location->contentId, $this->languages);
        }
        return $this->content;
    }

    /**
     * NB: if entity has been created from a Content, returns its MAIN location. Otherwise, the Location set at creation
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function location()
    {
        if($this->location == null){
            $this->location = $this->repository->getLocationService()->loadLocation($this->content->contentInfo->mainLocationId);
        }
        return $this->location;
    }
}
