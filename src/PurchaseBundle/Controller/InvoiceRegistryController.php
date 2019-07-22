<?php

namespace PurchaseBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use PurchaseBundle\Entity\Invoice;
use PurchaseBundle\Entity\InvoiceRegistryDiff;
use PurchaseBundle\Entity\InvoiceRegistry;
use PurchaseBundle\Entity\SupplierComment;
use PurchaseBundle\Entity\SupplierDiff;
use PurchaseBundle\Entity\SupplierPerson;
use PurchaseBundle\Exception\NonUniqueItnException;
use PurchaseBundle\Exception\NonUniqueTitleException;
use PurchaseBundle\Exception\SupplierExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use PurchaseBundle\Entity\Supplier;

class InvoiceRegistryController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     *
     * @Route("/registry/list", name="invoices_registry_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewRegistry() and !$this->getUser()->canEditRegistry()) {
            return $this->redirectToRoute('homepage');
        }

        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $registryList = $this->getInvoiceRegistryRepository()->getRegistryList($filters, $currentPage, self::PER_PAGE);

        $maxRows = $registryList->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('registry/list.html.twig', [
            'registryList' => $registryList,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
        ]);
    }

    /**
     * Finds and displays details.
     *
     * @Route("/registry/{id}/details", name="invoice_registry_details")
     */
    public function detailsAction (Request $request)
    {
        $registryId = $request->get('id');
        /** @var InvoiceRegistry $registry */
        $registry = $this->getInvoiceRegistryRepository()->find($registryId);
        $registryInvoices = $this->getInvoiceRegistryRepository()->getGroupedRegistryInvoices($registry);

        $registryChanges = $this->getInvoiceRegistryDiffRepository()->getRegistryChanges($registry);

        $invoices = $this->getInvoiceRepository()->findBy([
            'status' => [Invoice::STATUS_NEW, Invoice::STATUS_READY_TO_PAY, Invoice::STATUS_PAID]
        ]);

        return $this->render('registry/details.html.twig', [
            'registry' => $registry,
            'registryInvoices' => $registryInvoices,
            'invoices' => $invoices,
            'registryChanges' => $registryChanges
        ]);
    }

    /**
     * Add registry form.
     *
     * @Route("/invoice/add", name="invoice_registry_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $registryDetails = $request->get('registry');

        try {
            if (!empty($registryDetails)) {
                $registry = new InvoiceRegistry();

                $registry
                    ->setStatus(InvoiceRegistry::STATUS_NEW)
                    ->setCreatedAt(new \DateTime())
                    ->setOwner($this->getUser())
                    ->setMoneyLimit($registryDetails['moneyLimit']);

                $em = $this->getEm();
                $em->persist($registry);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Attach invoice to registry form.
     *
     * @Route("/invoice/{id}/attach", name="registry_attach_invoice")
     */
    public function attachInvoiceAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $registryId = $request->get('id');
        $invoices = $request->get('invoices');

        /** @var InvoiceRegistry $invoiceRegistry */
        $invoiceRegistry = $this->getInvoiceRegistryRepository()->find($registryId);

        try {
            $em = $this->getEm();
            foreach ($invoices as $invoiceId) {
                $changes = false;

                $invoice = $this->getInvoiceRepository()->find($invoiceId);

                if ($invoice->getInvoiceRegistry() == $invoiceRegistry){
                    $changes['attach_invoice'][] = $invoice->getInvoiceNumber();
                } else {
                    $changes['attach_invoice'][] = false;
                }

                $invoice->setInvoiceRegistry($invoiceRegistry);
                $em->persist($invoice);

                $changes['attach_invoice'][] = $invoice->getInvoiceNumber();
                $this->logChanges($invoiceRegistry, $changes);
            }
            $em->flush();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/invoice/{id}/attach-to-registry", name="attach_to_registry")
     */
    public function attachToRegistry(Request $request)
    {
        $invoiceDetails = $request->get('invoiceDetails');
        $invoiceRegistry = $this->getInvoiceRegistryRepository()->find($invoiceDetails['registry']);

        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceRepository()->find($request->get('id'));

        if ($this->getUser()->canEditRegistry()) {
            $invoice->setInvoiceRegistry($invoiceRegistry);

            $this->getEm()->persist($invoice);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/invoice/{id}/specify-amount-payment", name="specify_amount_payment")
     */
    public function specifyAmountPaymentAction(Request $request)
    {
        $invoiceDetails = $request->get('invoiceDetails');
        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceRepository()->find($request->get('id'));
        /** @var User $user */
        $user = $this->getUser();

        if ($user->canEditRegistry()) {
            $invoice->setAmountPaid($invoiceDetails['amountPaid']);

            $this->getEm()->persist($invoice);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/invoice/{id}/paid", name="invoice_paid")
     */
    public function invoicePaidAction(Request $request)
    {
        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceRepository()->find($request->get('id'));
        /** @var User $user */
        $user = $this->getUser();

        if ($user->canEditRegistry()) {
            $invoice->setStatus(Invoice::STATUS_PAID);

            $this->getEm()->persist($invoice);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
//
//    /**
//     * Edit request form.
//     *
//     * @Route("/suppliers/{id}/edit", name="suppliers_edit")
//     */
//    public function editAction(Request $request)
//    {
//        $flashbag = $this->get('session')->getFlashBag();
//        $flashbag->clear();
//
//        $supplierId = $request->get('id');
//        $supplierDetails = $request->get('supplier');
//        $clientId = $supplierDetails['client'];
//
//        /** @var Supplier $supplier */
//        $supplier = $this->getSupplierRepository()->find($supplierId);
//        /** @var Client $client */
//        $client = $this->getClientRepository()->find($clientId);
//
//        try {
//            if (!empty($supplier)) {
//                $this->validateSupplier($supplier, $supplierDetails);
//
//                $supplier = $this->buildSupplier($supplier, $client, $supplierDetails);
//
//                $em = $this->getEm();
//                $em->persist($supplier);
//
//                $uof = $em->getUnitOfWork();
//                $uof->computeChangeSets();
//
//                $this->logChanges($supplier, $uof->getEntityChangeSet($supplier));
//                $em->flush();
//            }
//        } catch (\Exception $exception) {
//            $flashbag->add('danger', $exception->getMessage());
//
//            throw $exception;
//        }
//
//        return $this->redirect($request->headers->get('referer'));
//    }

    /**
     * @param Supplier $supplier
     * @param Client $client
     * @param $supplierDetails
     * @return Supplier
     */
    protected function buildSupplier(Supplier $supplier, $client, $supplierDetails)
    {
        if ($client) {
            $supplier->setClient($client);
        }

        if (!$supplier->getId()) {
            $supplier->setCreatedBy($this->getUser());
        }

        $supplier
            ->setTitle($supplierDetails['title'])
            ->setFullTitle($supplierDetails['fullTitle'])
            ->setLegalAddress($supplierDetails['legalAddress'])
            ->setActualAddress($supplierDetails['actualAddress'])
            ->setPostalAddress($supplierDetails['postalAddress'])
            ->setEmail($supplierDetails['email'])
            ->setSite($supplierDetails['site'])
            ->setPhone($supplierDetails['phone'])
            ->setFax($supplierDetails['fax'])
            ->setOgrn($supplierDetails['ogrn'])
            ->setItn($supplierDetails['itn'])
            ->setKpp($supplierDetails['kpp'])
            ->setOkpo($supplierDetails['okpo'])
            ->setOkved($supplierDetails['okved'])
            ->setOkfs($supplierDetails['okfs'])
            ->setOkopf($supplierDetails['okopf'])
            ->setOkato($supplierDetails['okato'])
            ->setDirector($supplierDetails['director'])
            ->setBasis($supplierDetails['basis'])
            ->setAccountant($supplierDetails['accountant'])
            ->setRegisteredAt(new \DateTime($supplierDetails['registeredAt']))
            ->setCheckingAccount($supplierDetails['checkingAccount'])
            ->setBankShortName($supplierDetails['bankShortName'])
            ->setBankFullName($supplierDetails['bankFullName'])
            ->setCorrespondentAccount($supplierDetails['correspondentAccount'])
            ->setBic($supplierDetails['bic'])
            ->setBankMailingAddress($supplierDetails['bankMailingAddress'])
            ->setBankLegalAddress($supplierDetails['bankLegalAddress'])
            ->setBankActualAddress($supplierDetails['bankActualAddress'])
            ->setBankItn($supplierDetails['bankItn'])
            ->setBankKpp($supplierDetails['bankKpp']);

        $supplierDetails['supplies-categories'] = !empty($supplierDetails['supplies-categories']) ?
            $supplierDetails['supplies-categories'] : [];

        foreach ($supplier->getSupplierCategories() as $category) {
            if (!($key = array_search($category->getId(), $supplierDetails['supplies-categories']))) {
                $supplier->getSupplierCategories()->removeElement($category);
            } else {
                unset($supplierDetails['supplies-categories'][$key]);
            }
        }

        foreach ($supplierDetails['supplies-categories'] as $categoryId) {
            $category = $this->getSuppliesCategoryRepository()->find($categoryId);
            $supplier->getSupplierCategories()->add($category);
        }

        return $supplier;
    }

    /**
     * @param $invoiceRegisty
     * @param $changeSet
     * @return array
     */
    protected function logChanges($invoiceRegisty, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $invoiceRegistryDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);

            if ($oldValue != $newValue) {
                $invoiceRegistryDiff = new InvoiceRegistryDiff();

                $invoiceRegistryDiff
                    ->setChangedBy($this->getUser())
                    ->setInvoiceRegistry($invoiceRegisty)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($invoiceRegistryDiff);
                $invoiceRegistryDiffs[] = $invoiceRegistryDiff;
            }
        }

        return $invoiceRegistryDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        } elseif (!$value) {
            $value = 'No';
        } elseif ($value === true) {
            $value = 'Yes';
        }

        return $value;
    }
}