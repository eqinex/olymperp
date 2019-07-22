<?php
/**
 * Created by PhpStorm.
 * User: pedectrian
 * Date: 27.07.17
 * Time: 18:33
 */

namespace PurchaseBundle\Service\Export;

use Doctrine\ORM\PersistentCollection;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\PurchaseConstants;

class ProjectRequestsExportBuilder
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
     * @param PurchaseRequest[] $requests
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($requests)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $firstItem = current($requests);
        $project = $firstItem ? $firstItem->getProject() : null;

        $headerRow = 1;

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

        $cellGreenColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => '90EE90']
        ];

        $cellBlueColor = [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => ['rgb' => '75bbfd']
        ];

        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(30);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

        $this->phpExcel->getActiveSheet()->setCellValue('N' . $headerRow, 'Итого:');
        $this->phpExcel->getActiveSheet()->getStyle('N' . $headerRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('O' . $headerRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('N' . $headerRow)->getFont()->setBold(true);

        $headerRow++;

        $this->phpExcel->getActiveSheet()->setCellValue('A' . $headerRow, 'Наименование проекта: ' . $project->getName());
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $headerRow . ':D' . $headerRow);

        $headerRow++;
        $startBorderRow = $headerRow;

        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A' . $headerRow, '№ п/п')
            ->setCellValue('B' .$headerRow, '№ Заявки/отдел')
            ->setCellValue('C' . $headerRow, 'Счет')
            ->setCellValue('D' . $headerRow, 'Обозначение')
            ->setCellValue('E' . $headerRow, 'Наименование')
            ->setCellValue('F' . $headerRow, 'Кол-во')
            ->setCellValue('G' . $headerRow, 'Ед. Изм.')
            ->setCellValue('H' . $headerRow, 'Общая по счету')
            ->setCellValue('I' . $headerRow, 'Условия оплаты')
            ->setCellValue('J' . $headerRow, 'К оплате, всего')
            ->setCellValue('K' . $headerRow, 'Наличие договора, ТН, сч.ф')
            ->setCellValue('L' . $headerRow, 'Контрагент')
            ->setCellValue('M' . $headerRow, 'Отметка об оплате')
            ->setCellValue('N' . $headerRow, 'Сумма окончательного расчета')
            ->setCellValue('O' . $headerRow, 'Ответственное лицо')
            ->setCellValue('P' . $headerRow, 'Срок')
        ;

        $this->phpExcel->getActiveSheet()
            ->mergeCells('P' . $headerRow . ':Q' . $headerRow)
            ->mergeCells('A' . $headerRow . ':A' . ($headerRow +1))
            ->mergeCells('B' . $headerRow . ':B' . ($headerRow +1))
            ->mergeCells('C' . $headerRow . ':C' . ($headerRow +1))
            ->mergeCells('D' . $headerRow . ':D' . ($headerRow +1))
            ->mergeCells('E' . $headerRow . ':E' . ($headerRow +1))
            ->mergeCells('F' . $headerRow . ':F' . ($headerRow +1))
            ->mergeCells('G' . $headerRow . ':G' . ($headerRow +1))
            ->mergeCells('H' . $headerRow . ':H' . ($headerRow +1))
            ->mergeCells('I' . $headerRow . ':I' . ($headerRow +1))
            ->mergeCells('J' . $headerRow . ':J' . ($headerRow +1))
            ->mergeCells('K' . $headerRow . ':K' . ($headerRow +1))
            ->mergeCells('L' . $headerRow . ':L' . ($headerRow +1))
            ->mergeCells('M' . $headerRow . ':M' . ($headerRow +1))
            ->mergeCells('N' . $headerRow . ':N' . ($headerRow +1))
            ->mergeCells('O' . $headerRow . ':O' . ($headerRow +1))
        ;

        $headerRow++;

        $this->phpExcel->getActiveSheet()
            ->setCellValue('P' . $headerRow, 'План')
            ->setCellValue('Q' . $headerRow, 'Факт')
        ;

        $this->phpExcel->getActiveSheet()->getStyle('A' . ($headerRow -1) . ':Q' . $headerRow)->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getStyle('A' . ($headerRow -1) . ':Q' . $headerRow)->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;

        $headerRow++;
        $currentRow = $headerRow;
        $rowId = 1;

        $price = 0;

        $wares = [];
        /** @var Ware $ware */
        foreach ($project->getWares() as $ware) {
            $wares[] = $ware->getName();
        }

        if (!empty($wares)) {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(30);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, join(', ', $wares));
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':Q' . $currentRow)->getFill()->applyFromArray($cellBlueColor);

            $currentRow++;
        }

        foreach ($requests as $request) {
            $startRow = $currentRow;
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $request->getCode());
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $request->getDescription());
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getFont()->setItalic(true);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getFont()->setUnderline(true);
            $currentRow++;

            /** @var RequestItem $item */
            foreach ($request->getItems() as $item) {
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rowId);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $item->getInvoiceNumber());
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $item->getSku());
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $item->getTitle());
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $item->getActualQuantity() ?: $item->getQuantity());
                $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $item->getUnit());
                $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $item->getPrice());
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $item->getPrepaymentAmount());
                $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, (($item->getPrice() * $item->getPrepaymentAmount()) / 100));
                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, '-');
                $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $item->getSupplier());
                $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, '-');
                $this->phpExcel->getActiveSheet()->setCellValue('N' . $currentRow, $item->getPrice() - (($item->getPrice() * $item->getPrepaymentAmount()) / 100));
                $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                if ($request->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                    $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getFill()->applyFromArray($cellGreenColor);
                }
                $this->phpExcel->getActiveSheet()->setCellValue('O' . $currentRow, $request->getOwner()->getLastNameWithInitials());
                $this->phpExcel->getActiveSheet()->setCellValue('P' . $currentRow, $item->getEstimatedShipmentTime() . 'дн.');
                if ($item->getStockStatus()) {
                    $this->phpExcel->getActiveSheet()->setCellValue('Q' . $currentRow, 'Склад');
                    $this->phpExcel->getActiveSheet()->getStyle('Q' . $currentRow)->getFill()->applyFromArray($cellGreenColor);
                }

                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':Q' . $currentRow)
                    ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':Q' . $currentRow)
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(40);

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
                    $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow . ':O' . $currentRow)->getFill()->applyFromArray([
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => [
                            'rgb' => $rowColor
                        ]
                    ]);

                    $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'Собственное производство');
                    $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, '-');
                    $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, '-');
                    $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, '-');
                    $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, '-');
                    $this->phpExcel->getActiveSheet()->setCellValue('N' . $currentRow, '-');
                }

                $rowId++;
                $currentRow += 1;

                $price += $item->getPrice();
            }

            if ($startRow < $currentRow) {
                $this->phpExcel->getActiveSheet()
                    ->mergeCells('B'. $startRow . ':B' . ($currentRow - 1));
            }
        }
        $this->phpExcel->getActiveSheet()->getStyle('A' . $startBorderRow . ':Q' . ($currentRow -1))->applyFromArray($allBordersStyle);
        $this->phpExcel->getActiveSheet()->setCellValue('O1', number_format($price));

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка заявок XLSX")
            ->setDescription("Отчет по заявкам проекта")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}