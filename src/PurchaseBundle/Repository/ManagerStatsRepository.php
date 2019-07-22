<?php

namespace PurchaseBundle\Repository;

/**
 * ManagerStatsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ManagerStatsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param \DateTime $date
     * @return mixed
     */
    public function getRequestsInProgress(\DateTime $date)
    {
        $dateStart = clone $date;
        $dateEnd = clone $date;

        $dateStart->setTime(0, 0, 0);
        $dateEnd->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('ms');

        $qb->select('ms');

        $qb
            ->where($qb->expr()->between('ms.statsDate', ':dateStart', ':dateEnd'))
            ->setParameters([
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd
            ]);

        $qb->orderBy('ms.assignedRequests', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
