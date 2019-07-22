<?php

namespace DocumentBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use DocumentBundle\Entity\Document;
use DocumentBundle\Entity\TechnicalMap;

class DocumentService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNeedsApproveDocuments(User $user)
    {
        return $this->getDocumentRepository()->getDocumentsCounter(Document::DOCUMENT_STATUS_NEEDS_APPROVE, $user);
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNeedsApproveTechnicalMaps(User $user)
    {
        return $this->getTechnicalMapRepository()->getTechnicalMapsCounter(TechnicalMap::TECHNICAL_MAP_STATUS_NEEDS_APPROVE, $user);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}