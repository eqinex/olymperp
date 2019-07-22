<?php

namespace DocumentBundle\Repository;

/**
 * DocumentSignatoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentSignatoryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $documentId
     * @param $signatories
     * @return array
     */
    public function findDeletedSignatories($documentId, $signatories)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->select('s')
            ->andWhere($qb->expr()->eq('s.document', $documentId))
            ->andWhere($qb->expr()->notIn('s.signatory', $signatories));

        return $qb->getQuery()->getResult();
    }
}