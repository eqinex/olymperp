<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 31.01.19
 * Time: 9:50
 */

namespace InfrastructureBundle\Repository;

class ComputerDiffRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $type
     * @param $computerId
     * @return mixed
     */
    public function getComputerChanges($type, $computerId)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c');
        $qb
            ->where($qb->expr()->eq('c.' . $type, ':computerId'))
            ->setParameter('computerId', $computerId);

        $qb->orderBy('c.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

}