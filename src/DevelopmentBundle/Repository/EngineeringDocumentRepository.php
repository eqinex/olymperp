<?php

namespace DevelopmentBundle\Repository;
use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;


class EngineeringDocumentRepository extends EntityRepository
{
    use RepositoryPaginatorTrait;


    /**
     * @param $filters
     * @param $orderBy
     * @param $order
     * @param int $currentPage
     * @param int $perPage
     * @return Paginator
     */
    public function getEngineeringDocumentCatalogs($filters, $orderBy, $order, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('edc');

        $qb->select('edc');

        if (!empty($orderBy)) {
            if ($orderBy == 'project') {
                $qb
                    ->leftJoin('edc.project', 'p')
                    ->orderBy('p.name', $order);
            } elseif ($orderBy == 'owner') {
                $qb
                    ->leftJoin('edc.owner', 'u')
                    ->orderBy('u.lastname', $order);
            } else {
                $qb->orderBy('edc.' . $orderBy, $order);
            }
        } else {
            $qb->orderBy('edc.inventoryNumber', 'ASC');
        }

        if (!empty($filters['inventoryNumber'])) {
            $qb
                ->andWhere($qb->expr()->like('edc.inventoryNumber', ':inventoryNumber'))
                ->setParameter('inventoryNumber', '%' . $filters['inventoryNumber'] . '%');
        }

        if (!empty($filters['designation'])) {
            $qb
                ->andWhere($qb->expr()->like('edc.designation', ':designation'))
                ->setParameter('designation', '%' . $filters['designation'] . '%');
        }

        if (!empty($filters['typeOfDocument'])) {
            $qb
                ->andWhere($qb->expr()->like('edc.typeOfDocument', ':typeOfDocument'))
                ->setParameter('typeOfDocument', '%' . $filters['typeOfDocument'] . '%');
        }

        if (!empty($filters['title'])) {
            $qb
                ->andWhere($qb->expr()->like('edc.title', ':title'))
                ->setParameter('title', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['notice'])) {
            $qb
                ->andWhere($qb->expr()->like('edc.notice', ':notice'))
                ->setParameter('notice', '%' . $filters['notice'] . '%');
        }

        if (!empty($filters['project'])) {
            $qb
                ->andWhere('edc.project = :project')
                ->setParameter('project', $filters['project']);
        }

        if (!empty($filters['owner'])) {
            $qb->andWhere('edc.owner = :owner');
            $qb->setParameter('owner', $filters['owner']);
        }

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }
}