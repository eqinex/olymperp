<?php
/**
 * Created by PhpStorm.
 * User: pedectrian
 * Date: 27.07.17
 * Time: 18:33
 */

namespace PurchaseBundle\Service\Export;

use Doctrine\ORM\PersistentCollection;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use PurchaseBundle\Entity\RequestItem;

class ItemExportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     */
    public function __construct($phpExcel)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
    }

    /**
     * @param $items
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build(PersistentCollection $items, $withFinancialInfo = false)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $firstItem = $items->first();
        $purchaseRequest = $firstItem ? $firstItem->getPurchaseRequest() : null;

        $headerRow = 1;
        $currentRow = 2;
        $rowId = 1;

        if ($purchaseRequest) {
            $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);
            $this->phpExcel->getActiveSheet()->setCellValue('A1', $purchaseRequest->getCode());
            $this->phpExcel->getActiveSheet()->setCellValue('C1', $purchaseRequest->getOwner()->getLastNameWithInitials());
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $purchaseRequest->getProject()->getName());
            $this->phpExcel->getActiveSheet()
                ->mergeCells('A1:B1');
            $this->phpExcel->getActiveSheet()
                ->mergeCells('D1:G1');
            $headerRow++;
            $currentRow++;
        }

        $this->fillHeaders($headerRow, $withFinancialInfo);

        foreach ($items as $item) {
            if ($item->getProductionStatus()) {
                continue;
            }
            /** @var RequestItem $item */
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $item->getId());
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $item->getSku());
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $item->getTitle());
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $item->getQuantity());
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $item->getUnit());
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $item->getCategory());
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow,
                $item->getPreferredShipmentDate() ? $item->getPreferredShipmentDate()->format('d.m.Y') : '-'
            );
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $item->getNotice());

            if ($withFinancialInfo) {
                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $item->getQuantity());
            }

            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);

            $currentRow += 1;
            $rowId++;
        }

        $this->phpExcel->getActiveSheet()->setTitle('План планов');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcel->setActiveSheetIndex(0);

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка позиций XLSX")
            ->setDescription("Отчет по позициям")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    protected function fillHeaders($headerRow, $withFinancialInfo = false)
    {
        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A' . $headerRow, 'id')
            ->setCellValue('B' . $headerRow, 'Артикул')
            ->setCellValue('C' . $headerRow, 'Наименование')
            ->setCellValue('D' . $headerRow, 'Кол-во')
            ->setCellValue('E' . $headerRow, 'Ед. Изм.')
            ->setCellValue('F' . $headerRow, 'Категория')
            ->setCellValue('G' . $headerRow, 'Желаемая дата поставки')
            ->setCellValue('H' . $headerRow, 'Примечание');

        if ($withFinancialInfo) {
            $this->phpExcel
                ->getActiveSheet()
                ->setCellValue('I' . $headerRow, 'Поставщик')
                ->setCellValue('J' . $headerRow, 'Счет')
                ->setCellValue('K' . $headerRow, 'Фактическое кол-во')
                ->setCellValue('L' . $headerRow, 'Стоимость')
                ->setCellValue('M' . $headerRow, 'Предоплата')
                ->setCellValue('N' . $headerRow, 'Срок доставки, дн')
                ->setCellValue('O' . $headerRow, 'Предварительная цена')
            ;
        }


        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);


        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(22);

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('L' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('M' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('N' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('O' . $headerRow)->getFont()->setBold(true);
    }
}