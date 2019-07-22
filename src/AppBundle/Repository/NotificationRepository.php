<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\QueryBuilder;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\PurchaseConstants;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryPaginatorTrait;

    /**
     * @param User $user
     * @param $filters
     * @param int $currentPage
     * @param int $perPage
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getUserNotifications(User $user, $filters, $currentPage, $perPage)
    {
        $qb = $this->createQueryBuilder('n');

        $qb
            ->select('n')
            ->where('n.owner = :owner')
            ->andWhere('n.readAt is null')
            ->setParameter('owner', $user)
            ->orderBy('n.createdAt', 'DESC');

        $qb = $this->applyFilters($qb, $filters);

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    /**
     * @param QueryBuilder $qb
     * @param $filters
     * @return QueryBuilder
     */
    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['type'])) {
            $qb
                ->andWhere('n.type = :type')
                ->setParameter('type', $filters['type'])
            ;
        }

        if (!empty($filters['sender']) ) {
            $qb
                ->andWhere('n.sender = :sender')
                ->setParameter('sender', $filters['sender'])
            ;
        }

        if (!empty($filters['title'])) {
            $qb
                ->andWhere(
                    $qb->expr()->like('n.title', ':title')
                )
                ->setParameter('title', '%' . $filters['title'] . '%')
            ;
        }

        if (!empty($filters['createdAt'])) {
            list($startAt, $endAt) = explode(' - ', $filters['createdAt']);

            $startAt = new \DateTime($startAt);
            $endAt = new \DateTime($endAt);

            $qb
                ->andWhere(
                    $qb->expr()->between('n.createdAt', ':startAt', ':endAt')
                )
                ->setParameter('startAt', $startAt)
                ->setParameter('endAt', $endAt)
            ;
        }

        return $qb;
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNotificationsCounter(User $user){
        $qb = $this->createQueryBuilder('n');

        $qb
            ->select('COUNT(n.id)')
            ->where('n.owner = :owner')
            ->andWhere('n.readAt is null')
            ->setParameter('owner', $user);

        return $qb->getQuery()->getSingleScalarResult();
    }
}