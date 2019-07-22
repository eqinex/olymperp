<?php

namespace PurchaseBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PurchaseBundle\Entity\SupplierIncident;
use PurchaseBundle\Entity\SupplierComment;
use PurchaseBundle\Entity\SupplierDiff;
use PurchaseBundle\Entity\SupplierLegalForm;
use PurchaseBundle\Entity\SupplierPerson;
use PurchaseBundle\Exception\NonUniqueItnException;
use PurchaseBundle\Exception\NonUniqueTitleException;
use PurchaseBundle\Exception\SupplierExistsException;
use PurchaseBundle\Service\Export\IncidentExportBuilder;
use PurchaseBundle\Service\Export\SuppliersExportBuilder;
use PurchaseBundle\Service\Import\SuppliersImport;
use PurchaseBundle\Service\SupplierService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use PurchaseBundle\Entity\Supplier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SupplierController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     *
     * @Route("/suppliers", name="suppliers_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewSupplier()) {
            return $this->redirectToRoute('homepage');
        }

        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $supplierNames = $this->getSupplierRepository()->findAll();

        $suppliers = $this->getSupplierRepository()->getSuppliers($filters, $currentPage, self::PER_PAGE);
        $clients = $this->getClientRepository()->findAll();
        $suppliesCategoriesGrouped = $this->getSuppliesCategoryRepository()->getCategoriesGroupedByParent();
        $legalForms = $this->getSupplierLegalFormRepository()->findAll();

        $maxRows = $suppliers->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('supplier/list.html.twig', [
            'suppliers' => $suppliers,
            'legalForms' => $legalForms,
            'supplierNames' => $supplierNames,
            'suppliesCategoriesGrouped' => $suppliesCategoriesGrouped,
            'clients' => $clients,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'criticalities' => SupplierIncident::getCriticalityChoices(),
        ]);
    }

    /**
     * Finds and displays details.
     *
     * @Route("/suppliers/details/{id}", name="suppliers_details")
     */
    public function detailsAction (Request $request)
    {
        $supplierId = $request->get('id');
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $documents = $this->getDocumentRepository()->findBy(['supplier' => $supplierId]);

        $clients = $this->getClientRepository()->findAll();
        $supplierPersons = $this->getSupplierPersonRepository()->findBy([
            'supplier' => $supplierId,
            'deleted' => 0
        ]);

        $supplierChanges = $this->getSupplierDiffRepository()->getSupplierChanges($supplier);

        $supplierComments = $this->getSupplierCommentRepository()->findBy(['supplier' => $supplier]);

        $invoices = $this->getInvoiceRepository()->findBy(['supplier' => $supplierId]);

        $incidents = $this->getSupplierIncidentRepository()->findBy(['supplier' => $supplierId]);

        $suppliesCategoriesGrouped = $this->getSuppliesCategoryRepository()->getCategoriesGroupedByParent();
        $legalForms = $this->getSupplierLegalFormRepository()->findAll();

        $projects = $this->getProjectRepository()->findBy(['supplier' => $supplierId]);

        return $this->render('supplier/details.html.twig', [
            'supplier' => $supplier,
            'legalForms' => $legalForms,
            'supplierComments' => $supplierComments,
            'supplierChanges' => $supplierChanges,
            'suppliesCategoriesGrouped' => $suppliesCategoriesGrouped,
            'documents' => $documents,
            'clients' => $clients,
            'supplierPersons' => $supplierPersons,
            'invoices' => $invoices,
            'projects' => $projects,
            'incidents' => $incidents,
            'criticalities' => SupplierIncident::getCriticalityChoices()
        ]);
    }

    /**
     * Add request form.
     *
     * @Route("/suppliers/add", name="suppliers_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierDetails = $request->get('supplier');
        $clientId = $supplierDetails['client'];
        /** @var Client $client */
        $client = $this->getClientRepository()->find($clientId);

        try {
            if (!empty($supplierDetails)) {
                $supplier = new Supplier();

                $this->validateSupplier($supplier, $supplierDetails);

                $supplier = $this->buildSupplier($supplier, $client, $supplierDetails);

                $em = $this->getEm();
                $em->persist($supplier);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit request form.
     *
     * @Route("/suppliers/{id}/edit", name="suppliers_edit")
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierId = $request->get('id');
        $supplierDetails = $request->get('supplier');
        $clientId = $supplierDetails['client'];

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);
        /** @var Client $client */
        $client = $this->getClientRepository()->find($clientId);

        try {
            if (!empty($supplier)) {
                $this->validateSupplier($supplier, $supplierDetails);

                $supplier = $this->buildSupplier($supplier, $client, $supplierDetails);

                $em = $this->getEm();
                $em->persist($supplier);

                $uof = $em->getUnitOfWork();
                $uof->computeChangeSets();

                $this->logChanges($supplier, $uof->getEntityChangeSet($supplier));
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());

            throw $exception;
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Add supplier to blacklist
     *
     * @Route("/suppliers/{id}/add-blacklist-supplier", name="add_blacklist_supplier")
     */
    public function addBlackListSupplierAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierId = $request->get('id');
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $supplierHasIncidents = count($supplier->getIncidents()) ? true : false;

        if(!$supplierHasIncidents) {
            $incidentData = $request->get('incident');
        }

        try {
            $em = $this->getEm();

            if (!$supplierHasIncidents and !empty($incidentData)){
                $incident = $this->buildSupplierIncident(new SupplierIncident(), $supplier, $incidentData);
                $em->persist($incident);
            }

            $supplier->setAddedToBlackList(true);

            $em->persist($supplier);
            $em->flush();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove supplier into blacklist
     *
     * @Route("/suppliers/{id}/remove-blacklist-supplier", name="remove_blacklist_supplier")
     */
    public function removeBlackListSupplierAction(Request $request)
    {
        $supplierId = $request->get('id');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $supplier->setAddedToBlackList(false);

        $em = $this->getEm();
        $em->persist($supplier);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit supplier incident
     *
     * @Route("/suppliers/{id}/edit-supplier-incident/{incidentId}", name="edit_supplier_incident")
     */
    public function editSupplierIncidentAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierId = $request->get('id');
        $incidentId = $request->get('incidentId');
        $incidentData = $request->get('incident');

        /** @var SupplierIncident $incident */
        $incident = $this->getSupplierIncidentRepository()->find($incidentId);
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        try {
            if (!empty($incidentData)) {

                $incident = $this->buildSupplierIncident($incident, $supplier, $incidentData);

                $em = $this->getEm();
                $em->persist($incident);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('suppliers_details', ['id' => $supplierId]);
    }

    /**
     * Delete supplier incident
     *
     * @Route("/suppliers/{id}/delete-supplier-incident/{incidentId}", name="delete_supplier_incident")
     */
    public function deleteSupplierIncidentAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierId = $request->get('id');
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        if (!(count($this->getSupplierIncidentRepository()->findBy(['supplier' => $supplier->getId()])) - 1) and $supplier->isAddedToBlackList()) {
            $flashbag->add('danger', 'Контрагент, находящийся в чёрном списке, должен иметь хотя бы один инцидент!');
        } else {
            $incidentId = $request->get('incidentId');
            /** @var SupplierIncident $incident */
            $incident = $this->getSupplierIncidentRepository()->find($incidentId);

            $em = $this->getEm();
            $em->remove($incident);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Add supplier to blacklist
     *
     * @Route("/suppliers/{id}/add-supplier-incidents", name="add_supplier_incidents")
     */
    public function addSupplierIncidentsAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $supplierId = $request->get('id');
        $incidentDetails = $request->get('incident');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        try {
            if (!empty($incidentDetails)) {
                $em = $this->getEm();

                foreach ($incidentDetails['incidents'] as $incidentData){
                    $incident = $this->buildSupplierIncident(new SupplierIncident(), $supplier, $incidentData);
                    $em->persist($incident);
                }

                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('suppliers_details', ['id' => $supplierId]);
    }

    /**
     * Export incidents action.
     *
     * @Route("/suppliers/{id}/export-supplier-incidents", name="export_supplier_incidents")
     */
    public function exportPurchaseItemsAction(Request $request)
    {
        $supplierId = $request->get('id');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $incidents = $supplier->getIncidents();
        $exportBuilder = new IncidentExportBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($incidents);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'supplier_incidents.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param SupplierIncident $incident
     * @param Supplier $supplier
     * @param $incidentData
     * @return SupplierIncident
     * @throws \Exception
     */
    public function buildSupplierIncident(SupplierIncident $incident, Supplier $supplier, $incidentData){

        $incident
            ->setSupplier($supplier)
            ->setComment($incidentData['comment'])
            ->setCriticality($incidentData['criticality'])
            ->setDate(new \DateTime($incidentData['date']));

        return $incident;
    }

    /**
     * New supplier person
     *
     * @Route("/suppliers/{id}/add-supplier-person", name="add_supplier_person")
     */
    public function addSupplierPersonAction(Request $request)
    {
        $supplierId = $request->get('id');
        $supplierPersonDetails = $request->get('supplierPerson');

        $supplierPerson = new SupplierPerson();

        $supplierPerson = $this->buildSupplierPerson($supplierPerson, $supplierId, $supplierPersonDetails);

        $this->getEm()->persist($supplierPerson);
        $this->getEm()->flush();

        return $this->redirectToRoute('suppliers_details', ['id' => $supplierId]);
    }

    /**
     * Remove document signatory
     *
     * @Route("/suppliers/{id}/supplier-person/{supplierPersonId}/delete", name="delete_supplier_person")
     */
    public function deleteSupplierPersonAction(Request $request)
    {
        $supplierId = $request->get('id');
        $supplierPersonId = $request->get('supplierPersonId');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        /** @var SupplierPerson $supplierPerson */
        $supplierPerson = $this->getSupplierPersonRepository()->find($supplierPersonId);
        $change['supplierPerson'] = [$supplierPerson->getLastNameWithInitials(), 'deleted'];

        $supplierPerson->setDeleted(true);
        $this->logChanges($supplier, $change);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit supplier person
     *
     * @Route("/suppliers/{id}/edit-supplier-person", name="edit_supplier_person")
     */
    public function editSupplierPersonAction(Request $request)
    {
        $supplierId = $request->get('id');
        $supplierPersonId = $request->get('supplierPersonId');
        $supplierPersonDetails = $request->get('supplierPerson');

        /** @var SupplierPerson $supplierPerson */
        $supplierPerson = $this->getSupplierPersonRepository()->findOneBy([
            'id' => $supplierPersonId
        ]);

        $supplierPerson = $this->buildSupplierPerson($supplierPerson, $supplierId, $supplierPersonDetails);

        $this->getEm()->persist($supplierPerson);
        $this->getEm()->flush();

        return $this->redirectToRoute('suppliers_details', ['id' => $supplierId]);
    }

    /**
     * @param Supplier $supplier
     * @param $client
     * @param $supplierDetails
     * @return Supplier
     * @throws \Exception
     */
    protected function buildSupplier(Supplier $supplier, $client, $supplierDetails)
    {
        if ($client) {
            $supplier->setClient($client);
        }

        if (!$supplier->getId()) {
            $supplier->setCreatedBy($this->getUser());
            $supplier = $this->generateSupplierCode($supplier);
            $supplier->setUpdatedAt(new \DateTime());
        }

        /** @var SupplierLegalForm $legalForm */
        $legalForm = $this->getSupplierLegalFormRepository()->find($supplierDetails['legalForm']);

        $supplier
            ->setTitle($supplierDetails['title'])
            ->setLegalForm($legalForm)
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
     * @param Supplier $supplier
     * @param $supplierDetails
     * @throws NonUniqueItnException
     * @throws NonUniqueTitleException
     */
    protected function validateSupplier(Supplier $supplier, $supplierDetails)
    {
        $supplierExists = false;

        if (!$supplier->getId() || ($supplier->getId() && $supplier->getItn() != $supplierDetails['itn'])) {
            $supplierExists = $this->getSupplierRepository()->findOneBy(['itn' => $supplierDetails['itn']]);
        }

        if ($supplierExists) {
            throw new NonUniqueItnException($this->get('translator'), $supplierDetails['itn']);
        }

        if (!$supplier->getId() || ($supplier->getId() && $supplier->getTitle() != $supplierDetails['title'])) {
            $supplierExists = $this->getSupplierRepository()->findOneBy(['title' => $supplierDetails['title']]);
        }

        if ($supplierExists) {
            throw new NonUniqueTitleException($this->get('translator'), $supplierDetails['title']);
        }
    }

    protected function buildSupplierPerson(SupplierPerson $supplierPerson, $supplierId, $supplierPersonDetails)
    {
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $supplierPerson
            ->setFirstname($supplierPersonDetails['firstname'])
            ->setMiddlename($supplierPersonDetails['middlename'])
            ->setLastname($supplierPersonDetails['lastname'])
            ->setSupplier($supplier)
            ->setRole($supplierPersonDetails['role'])
            ->setPhone($supplierPersonDetails['phone'])
            ->setEmail($supplierPersonDetails['email'])
        ;

        return $supplierPerson;
    }

    /**
     * @Route("/suppliers/{id}/comment", name="supplier_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $supplierId = $request->get('id');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $supplierComment = $this->addComment($supplier, $comment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($supplierComment);
        $em->flush();

        return $this->redirectToRoute('suppliers_details', ['id' => $supplier->getId()]);
    }

    /**
     * @Route("/suppliers/{id}/update-info", name="supplier_update_info")
     */
    public function updateSupplierInfoAction(Request $request)
    {
        $supplierId = $request->get('id');

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($supplierId);

        $supplierService = $this->get('service.supplier');

        $supplierService->updateSupplierInfo($supplier);

        return $this->redirectToRoute('suppliers_details', ['id' => $supplier->getId()]);
    }

    /**
     * @param Supplier $supplier
     * @param $comment
     * @return SupplierComment
     */
    protected function addComment(Supplier $supplier, $comment)
    {
        $supplierComment = new SupplierComment();
        $supplierComment->setCreatedAt(new \DateTime());
        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $supplierComment = $this->getSupplierCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $supplierComment;
        }

        $changes['comment'][] = $supplierComment->getCommentText();
        $supplierComment
            ->setOwner($this->getUser())
            ->setSupplier($supplier)
            ->setCommentText($comment['text'])
        ;
        $changes['comment'][] = $supplierComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getSupplierCommentRepository()->find($comment['reply-id']);
            $supplierComment->setParentComment($parentComment);
        }

        $this->logChanges($supplier, $changes);

        return $supplierComment;
    }

    /**
     * Import suppliers action.
     *
     * @Route("/suppliers/import-suppliers", name="import_suppliers")
     */
    public function importSuppliersAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canEditSupplier()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $importFile = $request->files->get('import_suppliers_file');
        $filePath = $this->moveFile($importFile);

        $importBuilder = new SuppliersImport($this->getDoctrine());
        $importBuilder->build($filePath);

        unlink($filePath);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Export suppliers action.
     *
     * @Route("/suppliers/export-suppliers", name="export_suppliers")
     */
    public function exportSuppliersAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canEditSupplier()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $suppliers = $this->getSupplierRepository()->getSupplierWithItn();

        $exportBuilder = new SuppliersExportBuilder();
        $xml = $exportBuilder->build($suppliers);
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml);

        $filename = 'Suppliers.xml';
        $tmp = tempnam('', 'suppliers');

        $dom->save($tmp);

        $headers = [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $response = new Response(file_get_contents($tmp), 200, $headers);

        unlink($tmp);

        return $response;
    }

    /**
     * @param $supplier
     * @param $changeSet
     * @return array
     */
    protected function logChanges($supplier, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $supplierDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $supplierDiff = new SupplierDiff();

                $supplierDiff
                    ->setChangedBy($this->getUser())
                    ->setSupplier($supplier)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($supplierDiff);
                $supplierDiffs[] = $supplierDiff;
            }
        }

        return $supplierDiffs;
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

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function moveFile(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();

        $filePath = sys_get_temp_dir() . '/' . $fileName;

        $file->move(
            sys_get_temp_dir(),
            $fileName
        );

        return $filePath;
    }

    /**
     * @param Supplier $supplier
     * @return Supplier
     * @throws \Doctrine\ORM\ORMException
     */
    protected function generateSupplierCode(Supplier $supplier)
    {
        do {
            $code = Uuid::uuid4()->toString();
            $supplierDuplicate = $this->getSupplierRepository()->findOneBy(['oneSUniqueCode' => $code]);
            if (!$supplierDuplicate) {
                $supplier->setOneSUniqueCode($code);
                $check = true;
            } else {
                $check = false;
            }

        } while ($check == false);

        $this->getEm()->persist($supplier);

        return $supplier;
    }
}