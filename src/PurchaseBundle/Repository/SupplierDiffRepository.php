<?php

namespace PurchaseBundle\Repository;

use PurchaseBundle\Entity\Supplier;

class SupplierDiffRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSupplierChanges(Supplier $supplier)
    {
        $qb = $this->createQueryBuilder('sp');

        $qb
            ->select('sp')
            ->where($qb->expr()->eq('sp.supplier', ':supplierId'))
            ->setParameter('supplierId', $supplier->getId());

        $qb->orderBy('sp.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}