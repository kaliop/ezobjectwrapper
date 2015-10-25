<?php

namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;

interface eZObjectWrapperFactoryInterface
{
    /**
     * Create an eZObjectWrapper object, or a child class of eZObjectWrapper, according to parameters set in eZObjectWrapper.yml
     * @param integer|Location|Content $source when integer, a Location Id is supposed
     * @return \Kaliop\eZObjectWrapperBundle\Core\eZObjectWrapperInterface
     * @throws \Exception and many others
     */
    public function build($source);

    /**
     * Returns an array of eZObjectWrapperInterface objects
     * @param array $sources can be Content, Location or
     * @return eZObjectWrapperInterface[]
     */
    public function buildFromArray(array $sources);

    /**
     * @param mixed $id
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromContentId($id);

    /**
     * @param mixed $remoteId
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromContentRemoteId($remoteId);

    /**
     * @param mixed $remoteId
     * @return eZObjectWrapperInterface
     * @throws \Exception
     */
    public function buildFromLocationRemoteId($remoteId);
}
