<?php

namespace DevelopmentBundle\Repository;

use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\EntityRepository;

class ProgrammingDocumentRepository extends EntityRepository
{
    use RepositoryPaginatorTrait;

    public function getProgrammingDocumentCatalogs($filters, $orderBy, $order, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('pdc');

        $qb->select('pdc');

        if (!empty($orderBy)) {
            if ($orderBy == 'project') {
                $qb
                    ->leftJoin('pdc.project', 'p')
                    ->orderBy('p.name', $order);
            } elseif ($orderBy == 'owner') {
                $qb
                    ->leftJoin('pdc.owner', 'u')
                    ->orderBy('u.lastname', $order);
            } elseif ($orderBy == 'type') {
                $qb
                    ->leftJoin('pdc.type', 't')
                    ->orderBy('t.name', $order);
            } else {
                $qb->orderBy('pdc.' . $orderBy, $order);
            }
        } else {
            $qb->orderBy('pdc.id', 'DESC');
        }

        if (!empty($filters['project'])) {
            $qb
                ->andWhere('pdc.project = :project')
                ->setParameter('project', $filters['project']);
        }

        if (!empty($filters['owner'])) {
            $qb->andWhere('pdc.owner = :owner');
            $qb->setParameter('owner', $filters['owner']);
        }

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }
}