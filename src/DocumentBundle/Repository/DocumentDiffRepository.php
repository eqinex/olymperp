<?php

namespace DocumentBundle\Repository;


use DocumentBundle\Entity\Document;

class DocumentDiffRepository extends \Doctrine\ORM\EntityRepository
{
    public function getDocumentChanges(Document $document)
    {
        $qb = $this->createQueryBuilder('dc');

        $qb
            ->select('dc')
            ->where($qb->expr()->eq('dc.document', ':documentId'))
            ->setParameter('documentId', $document->getId());

        $qb->orderBy('dc.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}