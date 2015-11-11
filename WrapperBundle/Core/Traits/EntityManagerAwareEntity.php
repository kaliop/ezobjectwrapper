<?php

namespace Kaliop\eZObjectWrapperBundle\Core\Traits;

trait EntityManagerAwareEntity
{
    /**
     * @var \Kaliop\eZObjectWrapperBundle\Core\EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @param \Kaliop\eZObjectWrapperBundle\Core\EntityManager $entityManager
     * @return $this
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return \Kaliop\eZObjectWrapperBundle\Core\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * An helper function made available for subclasses of Entity: returns the repository used for the current entity
     *
     * @return \Kaliop\eZObjectWrapperBundle\Core\RepositoryInterface
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepositoryByContentTypeId($this->content()->contentInfo->contentTypeId);
    }
}
