<?php

namespace DocumentBundle\Repository;

use DocumentBundle\Entity\TechnicalMap;

class TechnicalMapDiffRepository extends \Doctrine\ORM\EntityRepository
{
    public function getTechnicalMapChanges(TechnicalMap $technicalMap)
    {
        $qb = $this->createQueryBuilder('tmc');

        $qb
            ->select('tmc')
            ->where($qb->expr()->eq('tmc.technicalMap', ':technicalMapId'))
            ->setParameter('technicalMapId', $technicalMap->getId());

        $qb->orderBy('tmc.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}