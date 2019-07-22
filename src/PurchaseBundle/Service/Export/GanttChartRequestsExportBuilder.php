<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 22.03.19
 * Time: 11:15
 */

namespace PurchaseBundle\Service\Export;

use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\PurchaseConstants;

class GanttChartRequestsExportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * ProjectRequestsExportBuilder constructor.
     * @param $phpExcel
     */
    public function __construct($phpExcel)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
    }

    /**
     * @param $wares
     * @param PurchaseRequest[] $requests
     * @param $project
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($wares, $requests, $project)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $allBordersStyle = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $styleArray = [
            'font' => [
                'size' => 12,
                'name' => 'Times New Roman'
            ],
            'alignment' => [
                'wrap' => true
            ]
        ];

        $cellBlueColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => '75bbfd']
        ];

        $headerRow = 3;

        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->mergeCells('D' . $headerRow . ':G' . $headerRow);
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $headerRow, 'Диаграмма  Гантта по закупу');
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)->getFont()->setBold(true);

        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('D' . $headerRow . ':G' . $headerRow);
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $headerRow, date('d.m.Y'));
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $headerRow . ':C' . $headerRow);
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $headerRow, 'Наименование проекта: ' . $project->getName());
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);

        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $headerRow . ':F' . $headerRow);
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $headerRow, 'Цель проекта: ' . $project->getGoal() ? : '-');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);

        $headerRow++;
        $startBorderRow = $headerRow;

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);

        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A' . $headerRow, '№ п/п')
            ->setCellValue('B' .$headerRow, '№ Заявки')
            ->setCellValue('C' . $headerRow, 'Обозначение')
            ->setCellValue('D' . $headerRow, 'Наименование')
            ->setCellValue('E' . $headerRow, 'Кол-во')
            ->setCellValue('F' . $headerRow, 'Ед. Изм.')
            ->setCellValue('G' . $headerRow, 'Стоимость, руб.')
            ->setCellValue('J' . $headerRow, 'Поставщик')
            ->setCellValue('K' . $headerRow, 'Счет')
            ->setCellValue('L' . $headerRow, 'Срок поставки')
        ;

        $this->phpExcel->getActiveSheet()
            ->mergeCells('G' . $headerRow . ':I' . $headerRow)
            ->mergeCells('L' . $headerRow . ':M' . $headerRow)
            ->mergeCells('A' . $headerRow . ':A' . ($headerRow +1))
            ->mergeCells('B' . $headerRow . ':B' . ($headerRow +1))
            ->mergeCells('C' . $headerRow . ':C' . ($headerRow +1))
            ->mergeCells('D' . $headerRow . ':D' . ($headerRow +1))
            ->mergeCells('E' . $headerRow . ':E' . ($headerRow +1))
            ->mergeCells('F' . $headerRow . ':F' . ($headerRow +1))
            ->mergeCells('J' . $headerRow . ':J' . ($headerRow +1))
            ->mergeCells('K' . $headerRow . ':K' . ($headerRow +1))
        ;

        $headerRow++;
        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(30);

        $this->phpExcel->getActiveSheet()
            ->setCellValue('G' . $headerRow, 'Предоплата')
            ->setCellValue('H' . $headerRow, 'Постоплата')
            ->setCellValue('I' . $headerRow, 'Общая стоимость')
            ->setCellValue('L' . $headerRow, 'План')
            ->setCellValue('M' . $headerRow, 'Факт')
        ;

        $this->phpExcel->getActiveSheet()->getStyle('A' . ($headerRow -1) . ':M' . $headerRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('A' . ($headerRow -1) . ':M' . $headerRow)->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $headerRow++;
        $currentRow = $headerRow;
        $rowId = 1;
        $paymentTotal = 0;
        $purchaseSumCell = [];

        /** @var Ware $ware */
        foreach ($wares as $ware) {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(30);
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, mb_strtoupper($ware->getName()));
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':M' . $currentRow)->getFill()->applyFromArray($cellBlueColor);

            $currentRow++;

            $rows = $this->draw($ware->getPurchaseRequests(), $currentRow, $rowId, $paymentTotal, $purchaseSumCell);
            $currentRow = $rows['currentRow'];
            $rowId = $rows['rowId'];
            $paymentTotal = $rows['paymentTotal'];
            $purchaseSumCell = $rows['purchaseSumCell'];
        }
        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(30);
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, mb_strtoupper('Прочие ТМЦ'));
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':M' . $currentRow)->getFill()->applyFromArray($cellBlueColor);
        $currentRow++;
        $rows = $this->draw($requests, $currentRow, $rowId, $paymentTotal, $purchaseSumCell);
        $currentRow = $rows['currentRow'];
        $paymentTotal = $rows['paymentTotal'];
        $purchaseSumCell = $rows['purchaseSumCell'];
        $this->phpExcel->getActiveSheet()->getStyle('A' . $startBorderRow . ':M' . ($currentRow -1))->applyFromArray($allBordersStyle);
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(30);
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, mb_strtoupper('Итого по проекту:'));
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;

        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, '=' . join('+', $purchaseSumCell));
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $paymentTotal);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, '=I' . $currentRow . '-G' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        return $this->phpExcel;
    }

    protected function draw($purchaseRequests, $currentRow, $rowId, $paymentTotal, $purchaseSumCell)
    {
        $cellGreenColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => '90EE90']
        ];

        /** @var PurchaseRequest $purchaseRequest */
        foreach ($purchaseRequests as $purchaseRequest) {
            $startRow = $currentRow;
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $purchaseRequest->getCode());
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $purchaseRequest->getDescription());
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setUnderline(true);
            $currentRow++;

            $paymentPurchase = 0;
            $sumRow = $currentRow;
            /** @var RequestItem $item */
            foreach ($purchaseRequest->getItems() as $item) {
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rowId);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $item->getSku());
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $item->getTitle());
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $item->getActualQuantity() ?: $item->getQuantity());
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $item->getUnit());
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $paymentPurchase = $paymentPurchase + $item->getPrice();
                $paymentTotal = $paymentTotal + $item->getPrice();
                $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $item->getPrice());
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $item->getPrice());
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                if ($purchaseRequest->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFill()->applyFromArray($cellGreenColor);
                }

                $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, '=I' . $currentRow . '-G' . $currentRow);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


                $supplier = $item->getInvoice() ? $item->getInvoice()->getSupplier() : $item->getSupplier();
                $invoiceNumber = $item->getInvoice() ? $item->getInvoice()->getInvoiceNumber() : $item->getInvoiceNumber();

                $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $supplier);
                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $invoiceNumber);
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $item->getEstimatedShipmentTime() . ' дн.');
                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                if ($item->getStockStatus()) {
                    $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, 'Склад');
                    $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)
                        ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)
                        ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getFill()->applyFromArray($cellGreenColor);
                }

                if ($item->getProductionStatus()) {
                    switch ($item->getProductionStatus()) {
                        case RequestItem::PRODUCTION_STATUS_IN_PRODUCTION;
                            $rowColor = 'a7d3f2';
                            break;
                        case RequestItem::PRODUCTION_STATUS_PRODUCED;
                            $rowColor = 'deebcd';
                            break;
                        default:
                            $rowColor = 'FFFFFF';
                    }
                    $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow . ':M' . $currentRow)->getFill()->applyFromArray([
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => [
                            'rgb' => $rowColor
                        ]
                    ]);
                }

                $rowId++;
                $currentRow++;

            }

            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, 'Итого по заявке:');
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, '=SUM(G' . $sumRow . ':G' . ($currentRow - 1) . ')');
            $purchaseSumCell[] = 'G' . $currentRow;
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $paymentPurchase);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if ($purchaseRequest->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFill()->applyFromArray($cellGreenColor);
            }

            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, '=I' . $currentRow . '-G' . $currentRow);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


            $currentRow++;

            if ($startRow < $currentRow) {
                $this->phpExcel->getActiveSheet()
                    ->mergeCells('B'. $startRow . ':B' . ($currentRow - 1));
            }
        }

        $rows = [
            'currentRow' => $currentRow,
            'rowId' => $rowId,
            'paymentTotal' => $paymentTotal,
            'purchaseSumCell' => $purchaseSumCell
        ];
        return $rows;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка диаграммы Гантта XLSX")
            ->setDescription("Выгрузка диаграммы Гантта")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}