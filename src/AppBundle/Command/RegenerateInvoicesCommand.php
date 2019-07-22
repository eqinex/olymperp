<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use PurchaseBundle\Entity\Invoice;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestComment;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\PurchaseConstants;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegenerateInvoicesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:request:invoices:regenerate')
            ->setDescription('Regenerate invoices from scratch')
            ->setHelp('Regenerate invoices from scratch')
            ->addArgument('countRequests', InputArgument::OPTIONAL, 'How many purchase requests?')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig\Error\Error
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $purchaseRequests = $em->getRepository(PurchaseRequest::class)->findAll();

        $output->writeln(['================================', '']);

        $output->writeln(['Found ' . count($purchaseRequests) . ' purchase requests!...', '']);
        $output->writeln(['================================', '']);

        /** @var PurchaseRequest $purchaseRequest */
        foreach ($purchaseRequests as $purchaseRequest) {
            $invoices = [];
            foreach ($purchaseRequest->getItems() as $item) {
                if ($item->getInvoiceNumber()) {
                    $uniqueNr = $item->getSupplier() . $item->getInvoiceNumber();

                    if (!isset($invoices[$uniqueNr])) {
                        $invoices[$uniqueNr] = [
                            'amount' => 0,
                            'invoiceNr' => $item->getInvoiceNumber(),
                            'supplier' => $item->getSupplier(),
                            'items' => [],
                        ];
                    }

                    $invoices[$uniqueNr]['amount'] = $invoices[$uniqueNr]['amount'] + $item->getPrice();
                    $invoices[$uniqueNr]['items'][] = $item;
                }
            }

            foreach ($invoices as $invoiceData) {
                $invoice = new Invoice();

                $invoice
                    ->setAmount($invoiceData['amount'])
                    ->setStatus(Invoice::STATUS_NEW)
                    ->setCreatedAt(new \DateTime())
                    ->setInvoiceNumber($invoiceData['invoiceNr'])
                    ->setPurchaseRequest($purchaseRequest)
                    ->setSupplier($invoiceData['supplier'])
                ;

                if ($purchaseRequest->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                    $invoice->setStatus(Invoice::STATUS_PAID);
                    $invoice->setAmountPaid($invoice->getAmount());
                } else if ($purchaseRequest->getStatus() == PurchaseConstants::STATUS_REJECTED) {
                    $invoice->setStatus(Invoice::STATUS_REJECTED);
                }

                if ($purchaseRequest->getPurchasingManager()) {
                    $invoice->setOwner($purchaseRequest->getPurchasingManager());
                }

                $em->persist($invoice);

                foreach ($invoiceData['items'] as $item) {
                    $item->setInvoice($invoice);
                    $em->persist($item);
                }
            }
        }

        $em->flush();
    }

}