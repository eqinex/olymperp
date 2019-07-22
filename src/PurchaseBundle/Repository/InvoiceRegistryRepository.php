<?php

namespace PurchaseBundle\Repository;
use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\QueryBuilder;
use PurchaseBundle\Entity\InvoiceRegistry;

/**
 * InvoiceRegistryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRegistryRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryPaginatorTrait;

    /**
     * @param $filters
     * @param int $currentPage
     * @param int $perPage
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getRegistryList($filters, $currentPage = 1, $perPage = 20)
    {
        $qb = $this->createQueryBuilder('rl');

        $qb->select('rl');

        $qb->orderBy('rl.id', 'DESC');

        $paginator = $this->paginate($qb, $currentPage, $perPage);

        return $paginator;
    }

    /**
     * @param InvoiceRegistry $registry
     * @return array
     */
    public function getGroupedRegistryInvoices(InvoiceRegistry $registry)
    {
        $invoices = [];

        foreach ($registry->getInvoices() as $invoice) {
            if (!isset($invoices[$invoice->getPurchaseRequest()->getProject()->getId()])) {
                $invoices[$invoice->getPurchaseRequest()->getProject()->getId()] = [
                    'project' => $invoice->getPurchaseRequest()->getProject(),
                    'invoices' => []
                ];
            }

            $invoices[$invoice->getPurchaseRequest()->getProject()->getId()]['invoices'][] = $invoice;
        }

        return $invoices;
    }

    /**
     * @return array
     */
    public function getActualRegistry()
    {
        $qb = $this->createQueryBuilder('rl');

        $qb->select('rl');

        $qb->where('rl.status != :status')
            ->setParameter('status', 'rejected');

        return $qb->getQuery()->getResult();
    }
}