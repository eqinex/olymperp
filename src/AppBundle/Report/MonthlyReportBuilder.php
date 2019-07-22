<?php
/**
 * Created by PhpStorm.
 * User: pedectrian
 * Date: 27.07.17
 * Time: 18:33
 */

namespace AppBundle\Report;


use AppBundle\Entity\Project;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;

class MonthlyReportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * @var array
     */
    protected $currentYearCols;

    /**
     * @var array
     */
    protected $nextYearCols;

    /**
     * @var integer
     */
    protected $currentYear;
    /**
     * @var integer
     */
    protected $currentMonth;

    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     */
    public function __construct($phpExcel)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();

        $this->currentYearCols = range('F', 'Q');
        $this->nextYearCols = array_merge(range('R', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE']);

        $this->currentYear = (new \DateTime())->format('Y');
        $this->currentMonth = (new \DateTime())->format('m');
    }
    
    public function build($projects)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);
        $this->fillHeaders();

        $currentRow = 3;
        $rowId = 1;

        foreach ($projects as $project) {
            /** @var Project $project */
            $priority = $project->getPriority() ? Project::getPriorityChoices()[$project->getPriority()] : '-';

            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rowId);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $project->getName());
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $project->getLeader()->getLastName());
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $priority);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'план');
            $this->phpExcel->getActiveSheet()->setCellValue('E' . ($currentRow + 1), 'факт');

            $this->phpExcel->getActiveSheet()
                ->mergeCells('A' . $currentRow . ':A' . ($currentRow + 1))
                ->mergeCells('B' . $currentRow . ':B' . ($currentRow + 1))
                ->mergeCells('C' . $currentRow . ':C' . ($currentRow + 1))
                ->mergeCells('D' . $currentRow . ':D' . ($currentRow + 1));

            $endYear = $project->getEndAt()->format('Y');
            $endMonth = $project->getEndAt()->format('m');

            $this->fillMonthPlan($this->currentYearCols, $endYear, $endMonth, $currentRow, $this->currentYear);
            $this->fillMonthFact($this->currentYearCols, $endYear, $endMonth, $currentRow, $this->currentYear);
            $this->fillMonthPlan($this->nextYearCols, $endYear, $endMonth, $currentRow, $this->currentYear + 1);

            $currentRow += 2;
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
            ->setTitle("Отчет")
            ->setSubject("Отчет XLSX")
            ->setDescription("Отчет по проектам")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    protected function fillHeaders()
    {
        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A1', 'п/п')
            ->setCellValue('B1', 'Проект')
            ->setCellValue('C1', 'Руководитель')
            ->setCellValue('D1', 'Приоритет')
            ->setCellValue('F1', '2017')
            ->setCellValue('R1', '2018');

        $this->phpExcel->getActiveSheet()
            ->mergeCells('A1:A2')
            ->mergeCells('B1:B2')
            ->mergeCells('C1:C2')
            ->mergeCells('D1:D2')
            ->mergeCells('F1:Q1')
            ->mergeCells('R1:AC1');

        for ($row = 0; $row < 12; $row++) {
            $this->phpExcel->getActiveSheet()
                ->setCellValue($this->currentYearCols[$row] . '2', $row + 1);
            $this->phpExcel
                ->getActiveSheet()
                ->getColumnDimension($this->currentYearCols[$row])->setWidth(4);
        }
        for ($row = 0; $row < 12; $row++) {
            $this->phpExcel->getActiveSheet()
                ->setCellValue($this->nextYearCols[$row] . '2', $row + 1);
            $this->phpExcel
                ->getActiveSheet()
                ->getColumnDimension($this->nextYearCols[$row])->setWidth(4);
        }

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);

        $this->phpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
    }

    protected function fillMonthPlan($columns, $endYear, $endMonth, $currentRow, $year)
    {
        for ($row = 0; $row < 12; $row++) {

            if ($endYear > $year) {
                $color = '84b547';
            } elseif ($endYear == $year) {
                if ($row < $endMonth - 1) {
                    $color = "84b547";
                } elseif ($row + 1 == $endMonth) {
                    $color = "000000";
                } else {
                    $color = null;
                }
            } else {
                $color = null;
            }

            if (!empty($color)) {
                $this->phpExcel->getActiveSheet()->getStyle($columns[$row] . $currentRow)->getFill()->applyFromArray([
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => [
                        'rgb' => $color
                    ]
                ]);
            }
        }
    }

    protected function fillMonthFact($columns, $endYear, $endMonth, $currentRow, $year)
    {
        for ($row = 0; $row < 12; $row++) {
            // blue
            $colorOne = '2c97de';
            // green
            $colorTwo = 'a9ea58';
            // red
            $colorThree = 'FF0000';

            if ($endYear == $year) {
                if ($row < $endMonth - 1 && $row < $this->currentMonth - 1) {
                    $color = $colorTwo;
                } elseif ($row >= $endMonth - 1 && $row <= $this->currentMonth - 1) {
                    $color = $colorThree;
                } elseif($row == $this->currentMonth - 1 && $row < $endMonth - 1) {
                    $color = $colorOne;
                } else {
                    $color = null;
                }
            } elseif($year < $endYear) {
                if ($row < $this->currentMonth - 1) {
                    $color = $colorTwo;
                } elseif ($row == $this->currentMonth - 1) {
                    $color = $colorOne;
                } else {
                    $color = null;
                }
            } else {
                $color = null;
            }

            if (!empty($color)) {
                $this->phpExcel->getActiveSheet()->getStyle($columns[$row] . ($currentRow+1))->getFill()->applyFromArray([
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => [
                        'rgb' => $color
                    ]
                ]);
            }
        }
    }


}