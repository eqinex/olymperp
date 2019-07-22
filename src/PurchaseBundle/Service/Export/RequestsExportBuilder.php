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
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class RequestsExportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * ProjectRequestsExportBuilder constructor.
     * @param $phpExcel
     * @param Translator $translator
     */
    public function __construct($phpExcel, $translator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
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

        $headerRow = 3;
        $currentRow = 5;
        $rowId = 1;

        $this->fillHeaders($headerRow);

        $price = 0;

        foreach ($requests as $request) {
            $requestPrice = 0;
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rowId);
            $this->phpExcel->getActiveSheet()->setCellValue(
                'B' . $currentRow, $request->getPriorityTitles()[$request->getPriority()]
            );
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $request->getCode());
            $this->phpExcel->getActiveSheet()->setCellValue(
                'D' . $currentRow, $this->translator->trans($request->getStatus())
            );
            $this->phpExcel->getActiveSheet()->setCellValue(
                'E' . $currentRow, $this->translator->trans($request->getPaymentStatus() ?: 'В работе ОС')
            );
            $this->phpExcel->getActiveSheet()->setCellValue(
                'F' . $currentRow, $this->translator->trans($request->getDeliveryStatus() ?: 'В работе ОС')
            );
            $this->phpExcel->getActiveSheet()->setCellValue(
                'G' . $currentRow, $request->getPurchasingManager() ?
                    $request->getPurchasingManager()->getLastNameWithInitials() : '-'
            );
            $this->phpExcel->getActiveSheet()->setCellValue(
                'H' . $currentRow, $request->getOwner()->getLastNameWithInitials()
            );
            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $request->getDescription());
            $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, count($request->getItems()));
            $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $request->getProject());

            $invoices = '';
            foreach ($request->getInvoicesList() as $key => $val) {
                $invoices = $invoices . $key . ' - ' . $val['supplier'] . ', ';
            }

            $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $invoices);
            $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, $request->getCreatedAt()->format('d.m.Y'));

            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow . ':B' . $currentRow)
                ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow . ':B' . $currentRow)
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /** @var RequestItem $item */
            foreach ($request->getItems() as $item) {
                $requestPrice += $item->getPrice();
                $price += $item->getPrice();
            }

            $this->phpExcel->getActiveSheet()->setCellValue('N' . $currentRow, $requestPrice);

            $rowId++;
            $currentRow++;
        }

        $this->phpExcel->getActiveSheet()->setCellValue('N1', number_format($price));

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка заявок XLSX")
            ->setDescription("Отчет по заявкам")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    protected function fillHeaders($headerRow)
    {
        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->setCellValue('M1', 'Итого:');
        $this->phpExcel->getActiveSheet()->getStyle('N1')
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->phpExcel->getActiveSheet()->getStyle('O1')
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->phpExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(18);

        $this->phpExcel->getActiveSheet()
            ->mergeCells('A2:D2');

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow . ':O' . $headerRow)
            ->getAlignment()->setWrapText(true);

        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow . ':O' . $headerRow)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow . ':O' . $headerRow)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A' . $headerRow, '№ п/п')
            ->setCellValue('B' . $headerRow, 'Пр-тет')
            ->setCellValue('C' . $headerRow, '№ Заявки')
            ->setCellValue('D' . $headerRow, 'Статус')
            ->setCellValue('E' . $headerRow, 'Статус оплаты')
            ->setCellValue('F' . $headerRow, 'Статус доставки')
            ->setCellValue('G' . $headerRow, 'Менеджер снабжения')
            ->setCellValue('H' . $headerRow, 'Владелец')
            ->setCellValue('I' . $headerRow, 'Описание')
            ->setCellValue('J' . $headerRow, 'Позиции')
            ->setCellValue('K' . $headerRow, 'Проект')
            ->setCellValue('L' . $headerRow, 'Счета')
            ->setCellValue('M' . $headerRow, 'Дата создания')
            ->setCellValue('N' . $headerRow, 'Сумма');

        $titleNum = $headerRow + 1;
        $this->phpExcel->getActiveSheet()->getStyle('A' . $titleNum . ':O' . $titleNum)
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $titleNum . ':O' . $titleNum)
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('B' . $titleNum, 1)
            ->setCellValue('C' . $titleNum, 2)
            ->setCellValue('D' . $titleNum, 3)
            ->setCellValue('E' . $titleNum, 4)
            ->setCellValue('F' . $titleNum, 5)
            ->setCellValue('G' . $titleNum, 6)
            ->setCellValue('H' . $titleNum, 7)
            ->setCellValue('I' . $titleNum, 8)
            ->setCellValue('J' . $titleNum, 9)
            ->setCellValue('K' . $titleNum, 10)
            ->setCellValue('L' . $titleNum, 11)
            ->setCellValue('M' . $titleNum, 12)
            ->setCellValue('N' . $titleNum, 13);

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(40);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);

//        $this->phpExcel->getActiveSheet()->getStyle('A' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('B' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('C' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('D' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('E' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('F' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('G' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('I' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('J' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('L' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('M' . $headerRow)->getFont()->setBold(true);
//        $this->phpExcel->getActiveSheet()->getStyle('N' . $headerRow)->getFont()->setBold(true);
    }
}