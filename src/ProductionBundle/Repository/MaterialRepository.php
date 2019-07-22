<?php

namespace ProductionBundle\Repository;

use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MaterialRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryPaginatorTrait;

    /**
     * @param $filters
     * @param int $currentPage
     * @param $perPage
     * @return Paginator
     */
    public function getMaterials($filters, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->select('m');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }
}