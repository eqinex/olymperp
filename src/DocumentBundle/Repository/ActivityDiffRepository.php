<?php

namespace DocumentBundle\Repository;

use DocumentBundle\Entity\Activity;

class ActivityDiffRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $activityId
     * @return mixed
     */
    public function getActivityChanges($activityId)
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->select('a')
            ->where($qb->expr()->eq('a.activity', ':activityId'))
            ->setParameter('activityId', $activityId);

        $qb->orderBy('a.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}