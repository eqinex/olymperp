<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 30.01.19
 * Time: 12:08
 */

namespace AppBundle\Service\Export;

use AppBundle\Entity\Project;
use AppBundle\Repository\RepositoryAwareTrait;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet_PageSetup;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\PurchaseConstants;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProjectRequestsBuilder
{
    use RepositoryAwareTrait;

    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * ProjectRequestsBuilder constructor.
     * @param $phpExcel
     * @param $translator
     * @param $router
     * @param $doctrine
     */
    public function __construct($phpExcel, $translator, $router, $doctrine)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
        $this->router = $router;
        $this->doctrine = $doctrine;
    }

    /**
     * @param $projects
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($projects)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);
        $styleArray = [
            'font' => [
                'size' => 12,
                'name' => 'Times new roman'
            ],
            'alignment' => [
                'wrap' => true
            ]
        ];

        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);

        $this->phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A2', $this->translator->trans('Project priority'));
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('A2:A3');

        $this->phpExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('B2', $this->translator->trans('Project'));
        $this->phpExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('B2:B3');

        $this->phpExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('C2', $this->translator->trans('№ П/П'));
        $this->phpExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('C2:C3');

        $this->phpExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('D2', $this->translator->trans('Request priority'));
        $this->phpExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('D2:D3');

        $this->phpExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('E2', '№ Заявки');
        $this->phpExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('E2:E3');

        $this->phpExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('F2', $this->translator->trans('Description'));
        $this->phpExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('F2:F3');

        $this->phpExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('G2', 'Затраты по приоритетам заявки, руб.');
        $this->phpExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('G2:J2');

        $this->phpExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('G3', PurchaseRequest::getPriorityTitles()[PurchaseRequest::PRIORITY_HIGHEST]);
        $this->phpExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('H3', PurchaseRequest::getPriorityTitles()[PurchaseRequest::PRIORITY_HIGH]);
        $this->phpExcel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('I3', PurchaseRequest::getPriorityTitles()[PurchaseRequest::PRIORITY_NORMAL]);
        $this->phpExcel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('J3', PurchaseRequest::getPriorityTitles()[PurchaseRequest::PRIORITY_LOW]);
        $this->phpExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('K2', 'Общая сумма, руб.');
        $this->phpExcel->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('K2:K3');

        $this->phpExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('L2', 'Счета');
        $this->phpExcel->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('L2:L3');

        $this->phpExcel->getActiveSheet()->getStyle('M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('M2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('M2', $this->translator->trans('Status'));
        $this->phpExcel->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('M2:M3');

        $this->phpExcel->getActiveSheet()->getStyle('N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('N2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('N2', $this->translator->trans('Supplier'));
        $this->phpExcel->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('N2:N3');

        $this->phpExcel->getActiveSheet()->getStyle('O2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('O2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('O2', $this->translator->trans('Payment status'));
        $this->phpExcel->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('O2:O3');

        $this->phpExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('P2', $this->translator->trans('Delivery status'));
        $this->phpExcel->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('P2:P3');

        $this->phpExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('Q2', $this->translator->trans('Purchasing manager'));
        $this->phpExcel->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('Q2:Q3');

        $this->phpExcel->getActiveSheet()->getStyle('R2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('R2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('R2', $this->translator->trans('Owner'));
        $this->phpExcel->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('R2:R3');

        $this->phpExcel->getActiveSheet()->getStyle('S2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('S2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('S2', $this->translator->trans('Created At'));
        $this->phpExcel->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->mergeCells('S2:S3');

        $currentRow = 5;

        $totalPrice = 0;
        $pricePriorityLow = 0;
        $pricePriorityNormal = 0;
        $pricePriorityHigh = 0;
        $pricePriorityHighest = 0;
        $purchaseCount = 1;

        $cellColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => '90EE90']
        ];

        $cellTotalColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => 'a0a0a0']
        ];

        /** @var Project $project */
        foreach ($projects as $project) {
            $projectPricePriorityLow = 0;
            $projectPricePriorityNormal = 0;
            $projectPricePriorityHigh = 0;
            $projectPricePriorityHighest = 0;
            $mergeRow = $currentRow;

            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $project->getPriorityChoices()[$project->getPriority()]);

            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $project->getName());
            $path =  $this->router->generate('project_details', ['id' => $project->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->phpExcel->getActiveSheet()->getCell('B' . $currentRow)->getHyperlink()->setUrl($path);

            /** @var PurchaseRequest $purchase */
            foreach ($project->getProjectPurchases() as $purchase) {

                if ($purchase->getStatus() == PurchaseConstants::STATUS_DONE or $purchase->getStatus() == PurchaseConstants::STATUS_REJECTED) {
                    continue;
                }
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $purchaseCount);

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $purchase->getPriorityTitles()[$purchase->getPriority()]);

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $purchase->getCode());
                $path = $this->router->generate('request_details', ['id' => $project->getId(), 'requestId' => $purchase->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
                $this->phpExcel->getActiveSheet()->getCell('E' . $currentRow)->getHyperlink()->setUrl($path);

                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $purchase->getDescription());

                $price = 0;

                /** @var RequestItem $item */
                foreach ($purchase->getItems() as $item) {
                    $price += $item->getPrice();
                }

                if ($purchase->getPriority() == PurchaseRequest::PRIORITY_HIGHEST) {
                    $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $price != 0 ? $price : '');
                    $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    if ($purchase->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getFill()->applyFromArray($cellColor);
                    } else {
                        $projectPricePriorityHighest += $price;
                    }
                } elseif ($purchase->getPriority() == PurchaseRequest::PRIORITY_HIGH) {
                    $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $price != 0 ? $price : '');
                    $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    if ($purchase->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFill()->applyFromArray($cellColor);
                    } else {
                        $projectPricePriorityHigh += $price;
                    }
                } elseif ($purchase->getPriority() == PurchaseRequest::PRIORITY_NORMAL) {
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $price != 0 ? $price : '');
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    if ($purchase->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFill()->applyFromArray($cellColor);
                    } else {
                        $projectPricePriorityNormal += $price;
                    }
                } elseif ($purchase->getPriority() == PurchaseRequest::PRIORITY_LOW) {
                    $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $price != 0 ? $price : '');
                    $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    if ($purchase->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getFill()->applyFromArray($cellColor);
                    } else {
                        $projectPricePriorityLow += $price;
                    }
                }

                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $price != 0 ? $price : '');
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                if ($purchase->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                    $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getFill()->applyFromArray($cellColor);
                }

                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $this->translator->trans($purchase->getInvoicePayment()));

                $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, $this->translator->trans($purchase->getStatus()));

                $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('N' . $currentRow, join(', ', $this->getPurchaseRequestSuppliers($purchase)));

                $this->phpExcel->getActiveSheet()->getStyle('O' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('O' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('O' . $currentRow, $this->translator->trans($purchase->getPaymentStatus()));

                $this->phpExcel->getActiveSheet()->getStyle('P' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('P' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('P' . $currentRow, $this->translator->trans($purchase->getDeliveryStatus()));

                $this->phpExcel->getActiveSheet()->getStyle('Q' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('Q' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('Q' . $currentRow, $purchase->getPurchasingManager() ? $purchase->getPurchasingManager()->getLastNameWithInitials() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('R' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('R' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('R' . $currentRow, $purchase->getOwner() ? $purchase->getOwner()->getLastNameWithInitials() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('S' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('S' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('S' . $currentRow, $purchase->getCreatedAt()->format('d.m.Y'));

                $currentRow++;
                $purchaseCount++;
            }

            $mergeLastRow = $currentRow == $mergeRow ? $currentRow : $currentRow - 1;

            $this->phpExcel->getActiveSheet()->mergeCells('A' . $mergeRow . ':A' . $mergeLastRow);
            $this->phpExcel->getActiveSheet()->mergeCells('B' . $mergeRow . ':B' . $mergeLastRow);

            $totalProjectPrice = $projectPricePriorityHighest + $projectPricePriorityHigh + $projectPricePriorityNormal + $projectPricePriorityLow;

            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $this->translator->trans('Total'));
            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $projectPricePriorityHighest);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $projectPricePriorityHigh);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $projectPricePriorityNormal);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $projectPricePriorityLow);
            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $totalProjectPrice);
            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getFont()->setBold(true);

            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':S' . $currentRow)->getFill()->applyFromArray($cellTotalColor);

            $pricePriorityHighest += $projectPricePriorityHighest;
            $pricePriorityHigh += $projectPricePriorityHigh;
            $pricePriorityNormal += $projectPricePriorityNormal;
            $pricePriorityLow += $projectPricePriorityLow;
            $totalPrice += $totalProjectPrice;

            $currentRow++;
        }

        $currentRow++;

        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $this->translator->trans('Total'));
        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $pricePriorityHighest);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $pricePriorityHigh);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $pricePriorityNormal);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $pricePriorityLow);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $totalPrice);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':S' . $currentRow)->getFill()->applyFromArray($cellTotalColor);

        return $this->phpExcel;
    }

    /**
     * @throws \PHPExcel_Exception
     */
    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка по заявкам с Олимпа")
            ->setDescription("Карта по заявкам с Олимпа")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @return mixed
     */
    public function getPurchaseRequestSuppliers(PurchaseRequest $purchaseRequest)
    {
        return $this->getRequestItemRepository()->getPurchaseRequestSuppliers($purchaseRequest);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}