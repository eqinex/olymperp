<?php


namespace PurchaseBundle\Service\Export;

use Doctrine\ORM\PersistentCollection;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use PurchaseBundle\Entity\Supplier;
use PurchaseBundle\Entity\SupplierIncident;

class IncidentExportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * IncidentExportBuilder constructor.
     * @param $phpExcel
     * @param $translator
     */
    public function __construct($phpExcel, $translator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
    }

    public function build(PersistentCollection $incidents){
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $firstIncident = $incidents->first();
        /** @var Supplier $supplier */
        $supplier = $firstIncident ? $firstIncident->getSupplier() : null;

        $headerRow = 1;
        $currentRow = 2;
        $rowId = 1;

        if ($supplier) {
            $supplierTitle = $supplier->getLegalForm() ? $supplier->getLegalForm()->getName() . ' "' . $supplier->getTitle() . '"' : $supplier->getTitle();

            $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(30);
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getFont()
                ->setBold(true)
                ->setSize(16)
            ;
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('A1', $supplierTitle);
            $this->phpExcel->getActiveSheet()->mergeCells('A1:D1');
            $headerRow++;
            $currentRow++;
        }

        $this->fillHeaders($headerRow);

        foreach ($incidents as $incident) {

            /** @var SupplierIncident $incident */
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $incident->getId());
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $incident->getComment());
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans($incident->getCriticalityChoices()[$incident->getCriticality()]));
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $incident->getDate()->format('d.m.Y'));

            $this->phpExcel->getActiveSheet()->getStyle('C2:D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C2:D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

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
            ->setSubject("Выгрузка инцидентов XLSX")
            ->setDescription("Выгрузка инцидентов")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    protected function fillHeaders($headerRow)
    {
        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A' . $headerRow, 'id')
            ->setCellValue('B' . $headerRow, 'Инцидент')
            ->setCellValue('C' . $headerRow, 'Ур. критичности')
            ->setCellValue('D' . $headerRow, 'Дата');

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);


        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A2:D' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A2:D' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    }
}