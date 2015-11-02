<?php


namespace Kaliop\eZObjectWrapperBundle\Core;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;

interface RepositoryInterface
{
    /**
     * @param Content $content
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    public function loadEntityFromContent(Content $content);

    /**
     * @param Location $location
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    public function loadEntityFromLocation(Location $location);

    /**
     * @param ContentInfo $contentInfo
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     */
    public function loadEntityFromContentInfo(ContentInfo $contentInfo);

    /**
     * @param int $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function loadEntityFromContentId($id);

    /**
     * An alias of loadEntityFromContentId, to keep the API friendly to Doctrine users
     * @param $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function find($id);

    /**
     * @param int $id
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If the specified location is not found
     */
    public function loadEntityFromLocationId($id);

    /**
     * @param string $remoteId
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the content with the given id does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the user has no access to read content and in case of un-published content: read versions
     */
    public function loadEntityFromContentRemoteId($remoteId);

    /**
     * @param string $remoteId
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityInterface
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read this location
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If the specified location is not found
     */
    public function loadEntityFromLocationRemoteId($remoteId);

    /**
     * Called by the Entity Manager when retrieving the repo service / creating the repo instance
     * @param string $contentTypeIdentifier
     * @return $this
     */
    public function setContentTypeIdentifier($contentTypeIdentifier);
}
