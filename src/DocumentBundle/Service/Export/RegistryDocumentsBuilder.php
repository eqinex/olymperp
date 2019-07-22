<?php

namespace DocumentBundle\Service\Export;

use AppBundle\Entity\User;
use DocumentBundle\Entity\Document;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet_PageSetup;

class RegistryDocumentsBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     * @param $tranlsator
     * @param User $user
     */
    public function __construct($phpExcel, $tranlsator,User $user)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $tranlsator;
        $this->user = $user;
    }

    /**
     * @param $documents
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($documents)
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

        $headerRow = 2;

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $headerRow, 'Утверждаю:');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('I' . $headerRow . ':J' . $headerRow);

        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $headerRow, 'АТ-48-1');

        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $headerRow, 'Генеральный директор');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('I' . $headerRow . ':J' . $headerRow);
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $headerRow, 'АО "НПО "Андроидная техника"');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('I' . $headerRow . ':J' . $headerRow);
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $headerRow, '______________ А.Ф. Пермяков');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('I' . $headerRow . ':J' . $headerRow);
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $headerRow, '"___" __________ 20___г.');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('I' . $headerRow . ':J' . $headerRow);
        $headerRow+=2;

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()
            ->setSize(14)
            ->setBold(true)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $headerRow, 'Реестр договоров с НПО "Андроидная техника"');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('A' . $headerRow . ':J' . $headerRow);
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()
            ->setSize(14)
            ->setBold(true)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $headerRow, '(Выписка по отсутствующим договорам)');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('A' . $headerRow . ':J' . $headerRow);

        $headerRow += 2;
        $currentRow = $headerRow;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(60);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, '№ п/п');

        $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $this->translator->trans('document.code'));

        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans('Status'));

        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $this->translator->trans('document.template'));

        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $this->translator->trans('Project'));

        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $this->translator->trans('Supplier'));

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, 'Дата заключения договора');

        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'Дата окончания действия договора');

        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $this->translator->trans('document.owner'));

        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $this->translator->trans('Created At'));

        $currentRow++;
        $count = 1;

        /** @var Document $document */
        foreach ($documents as $document) {
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $count);

            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $document->getCode());

            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans($document->getStatusList()[$document->getStatus()]));

            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $document->getDocumentTemplate() ? $document->getDocumentTemplate() : '-');

            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $document->getProject() ? $document->getProject()->getName() : '-');

            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $document->getSupplier() ? $document->getSupplier()->getTitle() : '-');

            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $document->getStartAt() ? $document->getStartAt()->format('d.m.Y') : '-');

            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $document->getEndAt() ? $document->getEndAt()->format('d.m.Y') : '-');

            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $document->getOwner()->getLastNameWithInitials());

            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $document->getCreatedAt() ? $document->getCreatedAt()->format('d.m.Y') : '-');

            $count++;
            $currentRow++;
        }

        $currentRow--;

        $borderStyle = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow . ':J' . $currentRow)->applyFromArray($borderStyle);

        $currentRow+=2;

        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'Исполнитель:');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('A' . $currentRow . ':B' . $currentRow);

        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, '___________________/___________________');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('E' . $currentRow . ':G' . $currentRow);

        $currentRow++;

        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'подпись/дата');
        $this->phpExcel->getActiveSheet()
            ->mergeCells('E' . $currentRow . ':G' . $currentRow);

        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $this->user->getLastNameWithInitials());

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка реестра договоров")
            ->setDescription("Выгрузка реестра договоров")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}