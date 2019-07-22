<?php

namespace AppBundle\Repository;
use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Traits\RepositoryPaginatorTrait;
/**
 * ITRequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ITRequestRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryPaginatorTrait;

    public function getAvailableRequests($filters, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r');

        $qb = $this->applyFilters($qb, $filters);

        $qb->orderBy('r.id', 'DESC');

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
        if (!empty($filters['status']) ||  (isset($filters['status']) && $filters['status'] === '0')) {
            $qb
                ->andWhere('r.status = :status')
                ->setParameter('status', $filters['status'])
            ;
        } else {
            $qb
                ->andWhere(
                    $qb->expr()->in('r.status', ':statuses')
                )
                ->setParameter('statuses', [0, 1])
            ;
        }

        if (!empty($filters['owner'])) {
            $qb
                ->andWhere('r.owner = :owner')
                ->setParameter('owner', $filters['owner'])
            ;
        }

        return $qb;
    }
}