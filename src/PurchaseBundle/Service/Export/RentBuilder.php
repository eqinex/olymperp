<?php


namespace PurchaseBundle\Service\Export;

use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use PurchaseBundle\Entity\Rent;

class RentBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;



    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     * @param $tranlsator
     */
    public function __construct($phpExcel, $tranlsator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $tranlsator;
    }

    /**
     * @param $rents
     * @param $date
     * @param $translator
     * @param $user
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($rents, $date, $translator, $user)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $currentRow = 4;
        $userFullName = mb_substr($user->getFirstname(),0,1,"UTF-8") . '.' . mb_substr($user->getMiddlename(),0,1,"UTF-8") . '.' . $user->getLastname();

        foreach (Rent::getMonthList() as $month => $num)
        {
            if ($date['month'] == $num)
            {
                $date['month'] = $translator->trans($month);
                break;
            }
        }

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);

        $this->phpExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);

        $this->phpExcel->getActiveSheet()->setCellValue('H1', 'AT-31-1');

        $this->phpExcel->getActiveSheet()->mergeCells("A2:H2");

        $this->phpExcel->getActiveSheet()->setCellValue('A2', 'Поступление  платежей от арендаторов за ' . $date['month'] . ' ' . $date['year'] . ' года');
        $total = 0;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(35);

        $this->phpExcel
            ->getActiveSheet()->getStyle('A' . $currentRow . ':H' . $currentRow)->getAlignment()->setWrapText(true);
        $this->phpExcel->getActiveSheet()->getStyle('A1:H' . $currentRow)->getAlignment()->setHorizontal(
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $this->phpExcel->getActiveSheet()->getStyle('A1:H' . $currentRow)->getAlignment()->setVertical(
            PHPExcel_Style_Alignment::VERTICAL_TOP
        );
        $this->phpExcel->getActiveSheet()->getStyle("A" . $currentRow . ":H" . $currentRow)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, "№ п/п");
        $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, "Наименование арендатора");
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, "Арендная плата, руб.");
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, "Отопление, руб.");
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, "Коммунальные платежи, руб.");
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, "Общая сумма поступлений, руб.");
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, "Площадь, кв.м.");
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, "Способ оплаты");

        $currentRow++;

        foreach ($rents as $rent)
        {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);

            $this->phpExcel->getActiveSheet()->getStyle("A" . $currentRow . ":H" . $currentRow)->applyFromArray(
                array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000')
                        )
                    )
                )
            );

            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rent->getId());
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $rent->getTenement()->getSupplier()->getTitle() . '(' . $rent->getTenement()->getTitle() . ')' );
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $rent->getRent());
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $rent->getHeating());
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $rent->getCommunalPayments());
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $rent->getTotal());
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $rent->getSquare());
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $translator->trans($rent->getMethod()));

            $total += $rent->getTotal();
            $currentRow++;
        }

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);

        $this->phpExcel->getActiveSheet()->getStyle("A" . $currentRow . ":H" . $currentRow)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );

        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'Итого');
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $total);

        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':H' . $currentRow)->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00')
                )
            )
        );

        $currentRow +=2;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->mergeCells("B" . $currentRow . ":D" . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':H' . ($currentRow + 1))->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, "Составил:              " . $user->getEmployeeRole() . " _______________________ " . $userFullName);

        $this->phpExcel->getActiveSheet()->getStyle('C' . ($currentRow + 1))->getFont()->setSize(10);
        $this->phpExcel->getActiveSheet()->setCellValue('C' . ($currentRow + 1) , 'подпись, дата');

        $this->phpExcel->setActiveSheetIndex(0);

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Арендаторы")
            ->setDescription("Арендаторы")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}