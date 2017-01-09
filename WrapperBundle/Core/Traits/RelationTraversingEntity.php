<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

use Kaliop\eZObjectWrapperBundle\Core\EntityInterface;

use eZ\Publish\Core\FieldType\RelationList;
use eZ\Publish\Core\FieldType\Relation;

/**
 * Adds the capability to fetch related objects as Entity.
 * ** Requires the EntityManagerAwareEntity trait as well **
 */
trait RelationTraversingEntity
{
    /**
     * @param string $fieldName
     * @return EntityInterface[]
     */
    protected function getRelations($fieldName)
    {
        $relatedEntities = array();

        $fieldValue = $this->content()->getFieldValue($fieldName);
        if (! $fieldValue instanceof RelationList\Value) {
            throw new \RuntimeException("Field '$fieldName' is not of type RelationList");
        }

        $relatedContentItems = array();
        foreach($fieldValue->destinationContentIds as $contentId) {
            // Just in case the object has been pu tin the trash or hidden and they have not updated the related fields to remove from view.
            try {
                $relatedContentItems[] = $this->getContentService()->loadContent($contentId);
            } catch (\Exception $e) {
            }
        }

        /** @var \Kaliop\eZObjectWrapperBundle\Core\EntityManager $em */
        $em = $this->getEntityManager();
        $relatedEntities = $em->loadMany($relatedContentItems);

        return $relatedEntities;
    }

    /**
     * @param string $fieldName
     * @return EntityInterface
     */
    protected function getRelation($fieldName)
    {
        $fieldValue = $this->content()->getFieldValue($fieldName);
        if (! $fieldValue instanceof Relation\Value) {
            throw new \RuntimeException("Field '$fieldName' is not of type Relation");
        }

        $relatedContentItem = $this->getContentService()->loadContent($fieldValue->destinationContentIds);

        /** @var \Kaliop\eZObjectWrapperBundle\Core\EntityManager $em */
        $em = $this->getEntityManager();
        $relatedEntity = $em->load($relatedContentItem);

        return $relatedEntity;
    }
}
