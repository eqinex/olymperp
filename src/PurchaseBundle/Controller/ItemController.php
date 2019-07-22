<?php

namespace PurchaseBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Utils\StringUtils;
use Doctrine\Common\Collections\ArrayCollection;
use PHPExcel_IOFactory;
use PurchaseBundle\Entity\Invoice;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestCategory;
use PurchaseBundle\Entity\PurchaseRequestDelivery;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\Entity\Supplier;
use PurchaseBundle\Entity\SuppliesCategory;
use PurchaseBundle\Entity\Unit;
use PurchaseBundle\Service\Export\ItemExportBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class ItemController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * Add purchase request items.
     *
     * @Route("/project/{id}/purchases/{requestId}/add-items", name="project_purchase_request_add_items")
     */
    public function addItemsAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $requestDetails = $request->get('request');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        foreach ($requestDetails['items'] as $itemData) {
            $item = $this->buildItem(new RequestItem(), $purchaseRequest, $itemData);
            $this->getEm()->persist($item);
        }

        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/edit-item/{itemId}", name="purchase_request_edit_item")
     */
    public function editItemAction(Request $request)
    {
        $itemId = $request->get('itemId');
        $requestId = $request->get('id');
        $itemData = $request->get('item');

        /** @var RequestItem $item */
        $item = $this->getRequestItemRepository()->find($itemId);
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);


        if ($item->getPurchaseRequest()->canEditItems($this->getUser()) && !empty($itemData)) {

            $oldItem = $item->getTitle() . ' (' . $item->getSku() .
                ') - ' . $item->getQuantity() . $item->getUnit();

            $item = $this->buildItem($item, $purchaseRequest, $itemData);
            $this->getEm()->persist($item);

            $newItem = $item->getTitle() . ' (' . $item->getSku() .
                ') - ' . $item->getQuantity() . $item->getUnit();

            $this->logChanges($item->getPurchaseRequest(), ['item' => [$oldItem, $newItem]]);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * Import items action.
     *
     * @Route("/purchases/{purchaseId}/import-items", name="purchase_request_import_items")
     */
    public function importItemsAction(Request $request)
    {
        $importFile = $request->files->get('import_items_file');
        $purchaseId = $request->get('purchaseId');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseId);

        $filePath = $this->moveFile($importFile, $purchaseRequest->getProject()->getId(), $purchaseId . '/import');

        $inputFileType = PHPExcel_IOFactory::identify($filePath);

        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

//  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++){

            if ($row == 2) {
                continue;
            }
            //  Read a row of data into an array
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);
            if (empty($item[0]) && empty($item[1])) {
                continue;
            }

            $itemData = [
                'sku' => $item[1] ?: '-',
                'title' => $item[2],
                'quantity' => $item[3],
                'unit' => $item[4],
                'category' => $item[5],
                'notice' => $item[7]
            ];

            $item = $this->buildItem(new RequestItem(), $purchaseRequest, $itemData);
            $this->getEm()->persist($item);
        }

        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param RequestItem $item
     * @param PurchaseRequest $purchaseRequest
     * @param $itemData
     * @return RequestItem
     */
    protected function buildItem(RequestItem $item, PurchaseRequest $purchaseRequest, $itemData)
    {
        /** @var SuppliesCategory $category */
        $category = $this->getSuppliesCategoryRepository()->findOneBy(['title' => $itemData['category']]);

        /** @var Unit $unit */

        if ((int)($itemData['unit'])) {
            $unit = $this->getUnitRepository()->find($itemData['unit']);
        } else {
            $unit = $this->getUnitRepository()->findOneBy(['title' => $itemData['unit']]);
        }

        if (isset($itemData['notice'])) {
            $item->setNotice($itemData['notice']);
        }

        $item
            ->setTitle($itemData['title'])
            ->setSku($itemData['sku'])
            ->setQuantity($itemData['quantity'])
            ->setUnit($unit)
            ->setSuppliesCategory($category)
            ->setPurchaseRequest($purchaseRequest);

        return $item;
    }

    /**
     * @Route("/purchases/{id}/add-notice/{itemId}", name="purchase_request_add_item_notice")
     */
    public function addItemNoticeAction(Request $request)
    {
        $itemId = $request->get('itemId');
        $itemData = $request->get('item');

        $purchaseItem = $this->getRequestItemRepository()->find($itemId);

        if (!empty($itemData['notice'])) {
            $purchaseItem->setNotice($itemData['notice']);

            $this->getEm()->persist($purchaseItem);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/remove-item/{itemId}", name="purchase_request_remove_item")
     */
    public function removeItemAction(Request $request)
    {
        $itemId = $request->get('itemId');
        $requestId = $request->get('id');

        /** @var RequestItem $item */
        $item = $this->getRequestItemRepository()->find($itemId);
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canEditItems($this->getUser())) {
            $this->removeItem($item);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/remove-items", name="purchase_request_remove_items")
     */
    public function removeItemsAction(Request $request)
    {
        $requestId = $request->get('id');
        $items = $request->get('items');
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canEditItems($this->getUser())) {
            foreach ($items as $id) {
                /** @var RequestItem $item */
                $item = $this->getRequestItemRepository()->find($id);
                $this->removeItem($item);
            }
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/move-items-to-production", name="purchase_request_move_items_to_production")
     */
    public function moveItemsToProductionAction(Request $request)
    {
        $requestId = $request->get('id');
        $items = $request->get('items');
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canProductionLeaderApprove($this->getUser())) {
            foreach ($items as $id) {
                /** @var RequestItem $item */
                $item = $this->getRequestItemRepository()->find($id);
                $this->moveItemToProduction($item);
            }

            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/mark-items-as-produced", name="purchase_request_mark_items_as_produced")
     */
    public function markItemsAsProducedAction(Request $request)
    {
        $requestId = $request->get('id');
        $items = $request->get('items');
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canProductionLeaderMarkItemAsProduced($this->getUser())) {
            foreach ($items as $id) {
                /** @var RequestItem $item */
                $item = $this->getRequestItemRepository()->find($id);

                if ($item->getProductionStatus() == RequestItem::PRODUCTION_STATUS_IN_PRODUCTION) {
                    $this->markItemAsProduced($item);
                }
            }

            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
    /**
     * @Route("/purchases/{id}/move-items-to-purchasing", name="purchase_request_move_items_to_purchasing")
     */
    public function moveItemsToPurchasingAction(Request $request)
    {
        $requestId = $request->get('id');
        $items = $request->get('items');
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canProductionLeaderApprove($this->getUser())) {
            foreach ($items as $id) {
                /** @var RequestItem $item */
                $item = $this->getRequestItemRepository()->find($id);
                $this->moveItemToPurchasing($item);
            }

            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/mark-items-on-stock", name="purchase_request_mark_items_on_stock")
     */
    public function markItemsOnStockAction(Request $request)
    {
        $requestId = $request->get('id');
        $items = $request->get('items');
        $status = $request->get('status');

        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if ($purchaseRequest->canMarkItemsOnstock($this->getUser())) {
            if ($status == RequestItem::STOCK_STATUS_ON_STOCK) {
                foreach ($items as $id) {
                    /** @var RequestItem $item */
                    $item = $this->getRequestItemRepository()->find($id);
                    $this->markItemOnStock($item);
                }
            } else {
                foreach ($items as $id) {
                    /** @var RequestItem $item */
                    $item = $this->getRequestItemRepository()->find($id);
                    $this->markItemNotOnStock($item);
                }
            }

            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param RequestItem $item
     */
    protected function removeItem(RequestItem $item)
    {
        $this->getEm()->remove($item);
        $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
            ') - ' . $item->getQuantity() . $item->getUnit();
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'Removed']]);
    }

    /**
     * @param RequestItem $item
     */
    protected function moveItemToProduction(RequestItem $item)
    {
        $item->setProductionStatus(RequestItem::PRODUCTION_STATUS_IN_PRODUCTION);
        $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
            ') - ' . $item->getQuantity() . $item->getUnit();
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'Moved to production']]);
    }

    /**
     * @param RequestItem $item
     */
    protected function moveItemToPurchasing(RequestItem $item)
    {
        $item->setProductionStatus(null);
        $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
            ') - ' . $item->getQuantity() . $item->getUnit();
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'Moved to purchasing']]);
    }

    /**
     * @param RequestItem $item
     */
    protected function markItemOnStock(RequestItem $item)
    {
        $item->setStockStatus(RequestItem::STOCK_STATUS_ON_STOCK);
        $item->setOnStockAt(new \DateTime());
        $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
            ') - ' . $item->getQuantity() . $item->getUnit();
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'On stock']]);
    }

    /**
     * @param RequestItem $item
     */
    protected function markItemNotOnStock(RequestItem $item)
    {
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$item->getStockStatus(), 'Not on stock']]);
        $item->setStockStatus(null);
        $item->setOnStockAt(null);
    }

    /**
     * @param RequestItem $item
     */
    protected function markItemAsProduced(RequestItem $item)
    {
        $item->setProductionStatus(RequestItem::PRODUCTION_STATUS_PRODUCED);
        $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
            ') - ' . $item->getQuantity() . $item->getUnit();
        $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'Produced']]);
    }

    /**
     * Import request items info action.
     *
     * @Route("/purchases/{purchaseId}/import-items-info", name="purchase_request_import_items_info")
     */
    public function importItemsInfoAction(Request $request)
    {
        $importFile = $request->files->get('import_items_file');
        $purchaseId = $request->get('purchaseId');
        /** @var FlashBag $flashbag */
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        $translator = $this->get('translator');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseId);

        $filePath = $this->moveFile($importFile, $purchaseRequest->getProject()->getId(), $purchaseId . '/import');

        $inputFileType = PHPExcel_IOFactory::identify($filePath);

        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $invoices = [];

//  Loop through each row of the worksheet in turn
        for ($row = 3; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);
            if (empty($item[0]) && empty($item[1])) {
                continue;
            }

            $itemId = $item[0];

            /** @var RequestItem $requestItem */
            $requestItem = $this->getRequestItemRepository()->findOneBy([
                'id' => $itemId,
                'purchaseRequest' => $purchaseId
            ]);

            if (!empty($item[9])) {

                /** @var Supplier $supplier */
                $supplier = $this->getSupplierRepository()->findOneBy(['title' => $item[8]]);

                if (isset($supplier)) {
                    /** @var Invoice $invoice */
                    $invoice = $this->getInvoiceRepository()->findOneBy([
                        'supplier' => $supplier->getId(),
                        'purchaseRequest' => $purchaseId,
                        'invoiceNumber' => $item[9]
                    ]);
                } else {
                    $flashbag->add('danger', sprintf($translator->trans('Supplier %s is not found'), $item[8]));
                    continue;
                }

                if (!$invoice) {
                    $invoice = new Invoice();
                    $invoice
                        ->setPurchaseRequest($purchaseRequest)
                        ->setStatus(Invoice::STATUS_NEW)
                        ->setInvoiceNumber($item[9])
                        ->setSupplier($supplier)
                        ->setCreatedAt(new \DateTime())
                        ->setOwner($this->getUser())
                    ;

                    $this->getEm()->persist($invoice);
                }
                $invoices[] = $invoice;

                $requestItem->setInvoice($invoice);

                $this->getEm()->persist($requestItem);
            }

            if ($requestItem) {
                /** @var Supplier $supplier */
                $supplier = $this->getSupplierRepository()->findOneBy(['title' => $item[8]]);
                if ($supplier) {
                    $requestItem->setSupplier($supplier);
                } else {
                    $flashbag->add('danger', sprintf($translator->trans('Supplier %s is not found'), $item[8]));
                }

                $requestItem
                    ->setInvoiceNumber($item[9])
                    ->setActualQuantity($item[10])
                    ->setPrice($item[11])
                    ->setPrepaymentAmount($item[12])
                    ->setEstimatedShipmentTime($item[13])
                    ->setPreliminaryEstimate($item[14])
                ;

                $this->getEm()->persist($requestItem);
            }

            $this->getEm()->flush();
        }

        if ($invoices) {
            foreach ($invoices as $invoice) {
                $this->getEm()->refresh($invoice);

                $invoice = $this->buildInvoiceAmount($invoice);

                $this->getEm()->persist($invoice);
            }
            $this->getEm()->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @param int $projectId
     * @param int $purchaseRequestId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, $projectId, $purchaseRequestId)
    {
        // Generate a unique name for the file before saving it
        $fileName = $file->getClientOriginalName();

        if ($file->getSize() > 102400000) {
            throw new \Exception("Максимальный размер файла 100MB");
        }

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('purchase_files_root_dir') . '/' . $projectId . '/' . $purchaseRequestId . '/'
            . $fileName;

        $file->move(
            $this->getParameter('purchase_files_root_dir') . '/' . $projectId . '/' . $purchaseRequestId,
            $fileName
        );

        return $filePath;
    }

    /**
     * Import items action.
     *
     * @Route("/purchases/{purchaseId}/export-purchase-items", name="purchase_request_export_purchase_items")
     */
    public function exportPurchaseItemsAction(Request $request)
    {
        $purchaseId = $request->get('purchaseId');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseId);

        $items = $purchaseRequest->getItems();
        $exportBuilder = new ItemExportBuilder($this->get('phpexcel'));

        $phpExcelObject = $exportBuilder->build($items, $this->getUser()->canViewFinancialInfo());

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            StringUtils::transliterate($purchaseRequest->getCode()) . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/purchases/edit-item-financial-info", name="edit_item_financial_info")
     * @Method({"POST"})
     */
    public function editItemFinancialInfo(Request $request)
    {
        $itemId = $request->get('itemId');
        $field = $request->get('field');
        $value = $request->get('value');
        $purchaseRequestId = $request->get('purchaseRequestId');

        /** @var PurchaseRequest $purchaseRequestId */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseRequestId);

        /** @var RequestItem $item */
        $item = $this->getRequestItemRepository()->find($itemId);

        if (!$purchaseRequest->canManagerPreliminaryEstimate($this->getUser()) && !$purchaseRequest->canManagerFinishWork($this->getUser())) {
            exit;
        }

        if ($field == 'price') {
            $item->setPrice(floatval($value));
        } elseif ($field == 'actual-quantity') {
            $item->setActualQuantity(intval($value));
        } elseif ($field == 'prepayment-amount') {
            $item->setPrepaymentAmount(intval($value));
        } elseif ($field == 'preliminary-estimate') {
            $item->setPreliminaryEstimate(floatval($value));
        } elseif ($field == 'estimated-shipment-time') {
            $item->setEstimatedShipmentTime(intval($value));
        }

        $this->getEm()->persist($item);
        $this->getEm()->flush();

        if ($item->getInvoice()) {
            $invoice = $item->getInvoice();
            $invoice = $this->buildInvoiceAmount($invoice);

            $this->getEm()->persist($invoice);
            $this->getEm()->flush();
        }

        $response = [
            'purchaseRequestId' => $purchaseRequestId,
            'itemId' => $itemId,
            'field' => $field,
            'value' => $value,
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/purchases/{id}/item/{itemId}/refresh-supplier", name="refresh_supplier")
     */
    public function refreshSupplier(Request $request)
    {
        $purchaseRequestId = $request->get('id');
        $itemId = $request->get('itemId');
        $itemDetails = $request->get('itemDetails');
        $em = $this->getEm();

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseRequestId);

        /** @var RequestItem $item */
        $item = $this->getRequestItemRepository()->find($itemId);

        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($itemDetails['supplier']);
        $invoiceNumber = $itemDetails['invoiceNumber'];

        /** @var Invoice $oldInvoice */
        $oldInvoice = $item->getInvoice();

        if (!$purchaseRequest->canManagerPreliminaryEstimate($this->getUser()) && !$purchaseRequest->canManagerFinishWork($this->getUser())) {
            exit;
        }

        if ($invoiceNumber) {
            /** @var Invoice $invoice */
            $invoice = $this->getInvoiceRepository()->findOneBy([
                'supplier' => $supplier->getId(),
                'invoiceNumber' => $invoiceNumber
            ]);

            if (!$invoice) {
                $invoice = new Invoice();

                $invoice
                    ->setSupplier($supplier)
                    ->setInvoiceNumber($invoiceNumber)
                    ->setCreatedAt(new \DateTime(date('d.m.Y H:i')))
                    ->setPurchaseRequest($purchaseRequest)
                    ->setStatus(Invoice::STATUS_NEW)
                    ->setOwner($this->getUser());
            }
            if ($oldInvoice) {
                $oldInvoice->getRequestItems()->removeElement($item);
                $oldInvoice = $this->buildInvoiceAmount($oldInvoice);
                $em->persist($oldInvoice);
            }

            $item
                ->setInvoice($invoice)
                ->setSupplier($supplier);
            $em->persist($item);

            $invoice->getRequestItems()->add($item);

            $invoice = $this->buildInvoiceAmount($invoice);
            $em->persist($invoice);

            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/purchases/{id}/item/{itemId}/add-delivery-item", name="purchase_request_add_delivery_item")
     */
    public function addDeliveryItem(Request $request)
    {
        $purchaseRequestId = $request->get('id');
        $itemId = $request->get('itemId');
        $deliveryDetails = $request->get('delivery');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseRequestId);
        /** @var RequestItem $item */
        $item = $this->getRequestItemRepository()->find($itemId);
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierRepository()->find($deliveryDetails['supplier']);
        /** @var City $cityFrom */
        $cityFrom = $this->getCityRepository()->find($deliveryDetails['cityFrom']);
        /** @var City $cityWhere */
        $cityWhere = $this->getCityRepository()->find($deliveryDetails['cityWhere']);

        $delivery = new PurchaseRequestDelivery();

        $delivery
            ->setOwner($this->getUser())
            ->setPurchaseRequest($purchaseRequest)
            ->setItem($item)
            ->setSupplier($supplier)
            ->setCityFrom($cityFrom)
            ->setCityWhere($cityWhere)
            ->setPrice($deliveryDetails['price'])
        ;

        $this->getEm()->persist($delivery);
        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/remove-inoice/{id}", name="remove_invoice")
     */
    public function removeInvoiceAction(Request $request)
    {
        $invoiceId = $request->get('id');

        /** @var RequestItem $item */
        $invoice = $this->getInvoiceRepository()->find($invoiceId);

        if ($invoice->canRemoveInvoice($this->getUser())) {
            $this->getEm()->remove($invoice);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Invoice $invoice
     * @return Invoice
     */
    public function buildInvoiceAmount(Invoice $invoice)
    {
        $amount = 0;
        /** @var RequestItem $item */
        foreach ($invoice->getRequestItems() as $item) {
            $amount += $item->getPrice();
        }

        $invoice->setAmount($amount);

        return $invoice;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param $changeSet
     * @return array
     */
    protected function logChanges(PurchaseRequest $purchaseRequest, $changeSet)
    {
        $em = $this->getEm();
        $purchaseRequestDiffs = [];
        foreach ($changeSet as $field => $changes) {
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue) {
                $purchaseRequestDiff = new PurchaseRequestDiff();

                $purchaseRequestDiff
                    ->setChangedBy($this->getUser())
                    ->setPurchaseRequest($purchaseRequest)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($purchaseRequestDiff);
                $purchaseRequestDiffs[] = $purchaseRequestDiff;
            }
        }

        return $purchaseRequestDiffs;
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
            $value = 'no';
        } elseif ($value === true) {
            $value = 'yes';
        }

        return $value;
    }

    protected function sendEmail($title, $recipient, $body)
    {
        $email = new \Swift_Message('[OLYMP]{Закупки} ' . $title);
        $email
            ->setFrom('olymp@npo-at.com')
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        $this->get('mailer')->send($email);
    }
}
