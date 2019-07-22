<?php

namespace AppBundle\Report;

use AppBundle\Entity\ProjectTask;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class GanttChartBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * @var \PHPExcel
     */
    protected $phpExcelService;

    /**
     * @var array
     */
    protected $dailyCols;

    /**
     * @var array
     */
    protected $dayCols;

    /**
     * GanttChartBuilder constructor.
     * @param \PHPExcel $phpExcel
     */
    public function __construct($phpExcel)
    {
        $this->phpExcelService = $phpExcel;
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->dailyCols = range('F', 'Z');
    }
    
    public function build(ProjectTask $epic)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);
        $this->fillHeaders($epic);

        $currentRow = 10;
        $rowId = 1;

        foreach ($epic->getEpicTasks() as $task) {

            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $rowId);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $task->getTitle());
            $this->phpExcel->getActiveSheet()->setCellValue(
                'E' . $currentRow,
                $task->getResponsibleUser()->getLastNameWithInitials());

            $nowDate = new \DateTime();
            $nowCol = isset($this->dayCols[$nowDate->format('d.m')]) ?
                $this->dayCols[$nowDate->format('d.m')] :
                false;

            if ($nowCol) {
                $this->phpExcel->getActiveSheet()->getStyle($nowCol . $currentRow)->applyFromArray(
                    [
                        'borders' => [
                            'allborders' => [
                                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '777777']
                            ]
                        ]
                    ]
                );
            }

            $startDate = $task->getStartAt();
            $endDate = $task->getEndAt();
            while ($startDate <= $endDate) {
                if ($task->getStatus() == ProjectTask::STATUS_CANCELLED) {
                    $startDate->modify('+1 day');
                    continue;
                } elseif ($task->getStatus() == ProjectTask::STATUS_DONE) {
                    $color = '008600';
                } elseif ($nowDate < $startDate) {
                    // Light green
                    $color = '92d050';
                } elseif ($nowDate > $endDate) {
                    // Red
                    $color = 'ff3333';
                } elseif ($nowDate->format('d.m') == $endDate->format('d.m')) {
                    // Yellow
                    $color = 'ffff66';
                } elseif ($nowDate >= $startDate && $nowDate < $endDate) {
                    if ($task->getStatus() == ProjectTask::STATUS_NEW ||
                        $task->getStatus() == ProjectTask::STATUS_READY_TO_WORK) {
                        // Yellow
                        $color = 'ffff66';
                    } else {
                        // Dark green
                        $color ='008600';
                    }
                }

                if (isset($this->dayCols[$startDate->format('d.m')])) {
                    $this->phpExcel->getActiveSheet()->getStyle($this->dayCols[$startDate->format('d.m')] . $currentRow)->getFill()->applyFromArray([
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => [
                            'rgb' => $color
                        ]
                    ]);
                }

                $startDate->modify('+1 day');
            }

            if (isset($this->dayCols[$epic->getEndAt()->format('d.m')])) {
                $this->phpExcel->getActiveSheet()->getStyle($this->dayCols[$epic->getEndAt()->format('d.m')] . $currentRow)->getFill()->applyFromArray([
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => [
                        'rgb' => '000000'
                    ]
                ]);
            }

            if (!in_array($task->getStatus(), [ProjectTask::STATUS_DONE, ProjectTask::STATUS_CANCELLED])) {
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getFont()->setBold(true);
            } else {
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getFont()->setStrikethrough(true);
            }

            $this->phpExcel
                ->getActiveSheet()
                ->getRowDimension($currentRow)->setRowHeight(20);

            $currentRow += 1;
            $rowId++;

        }

        $this->phpExcel->getActiveSheet()->setTitle('Ф21');
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

        $style = [
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10,
                'bold' => false,
                'color' => ['rgb' => '000000']
            ]
        ];

        $this->phpExcel->getActiveSheet()->getStyle('A1:Z256')->applyFromArray($style);

        $this->phpExcel->getActiveSheet()->getStyle("A8:Z256")->applyFromArray(
            [
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                        'color' => ['rgb' => '555555']
                    ]
                ]
            ]
        );

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    protected function fillHeaders(ProjectTask $epic)
    {
        // Fill ceo info
        $this->phpExcel->getActiveSheet()
            ->mergeCells('U1:V1')
            ->mergeCells('O2:Q2')
            ->mergeCells('O3:R3')
            ->mergeCells('O4:S4')
            ->mergeCells('F5:I5')
            ->mergeCells('O5:U5')
            ->mergeCells('A6:D6')
            ->mergeCells('L6:M6')
            ->mergeCells('O6:S6')
            ->mergeCells('A7:D7')
            ->mergeCells('C8:D8')
        ;
        $this->phpExcel->getActiveSheet()
            ->setCellValue('U1', 'AT-21-0')
            ->setCellValue('O2', 'УТВЕРЖДАЮ:')
            ->setCellValue('O3', 'Генеральный директор')
            ->setCellValue('O4', 'НПО "Андроидная техника"')
            ->setCellValue('F5', 'Диаграмма Гантта')
            ->setCellValue('O5', '_____________________А.Ф. Пермяков')
            ->setCellValue('A6', 'Наименование проекта: ' . $epic->getProject()->getName())
            ->setCellValue('L6', 'дата печати')
            ->setCellValue('O6', '«___» _________ 20__г.')
            ->setCellValue('A7', 'Цель: ' . $epic->getTitle())
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->setCellValue('A8', '№ п/п')
            ->setCellValue('B8', 'Наименование операций')
            ->setCellValue('C8', 'Бюджет, тыс. руб.')
            ->setCellValue('E8', 'Ответственный')
            ->setCellValue('C9', 'План')
            ->setCellValue('D9', 'Факт')
        ;

        $this->phpExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('R')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('S')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('T')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('U')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('V')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('W')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('X')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(5);
        $this->phpExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(5);

        $this->phpExcel
            ->getActiveSheet()
            ->getRowDimension(8)->setRowHeight(35);

        $this->phpExcel
            ->getActiveSheet()->getStyle('A8:Z8')->getAlignment()->setWrapText(true);

        $this->phpExcel->getActiveSheet()->getStyle('A8:Z8')
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A8:Z8')
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C9:D9')
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C9:D9')
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $this->phpExcel->getActiveSheet()->getStyle('F8:Z8')
            ->getAlignment()->setTextRotation(90);

        // Set dates

        $startDate = $epic->getStartAt();
        $endDate = $epic->getEndAt();
        foreach ($this->dailyCols as $col) {
            if ($startDate >= $endDate) {
                break;
            }

            $this->phpExcel->getActiveSheet()
                ->setCellValue($col . '8', $startDate->format('d.m'));

            if ($this->isWeekend($startDate->format('Y-m-d'))) {
                $this->phpExcel->getActiveSheet()->getStyle($col . '8')->getFill()->applyFromArray([
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => [
                        'rgb' => '99CCFF'
                    ]
                ]);
            }

            $this->dayCols[$startDate->format('d.m')] = $col;

            $startDate->modify('+1 day');
        }

    }


    public function isWeekend($date)
    {
        $weekDay = date('w', strtotime($date));

        return ($weekDay == 0 or $weekDay == 6);
    }


    public function getResponse()
    {
        $writer = $this->phpExcelService->createWriter($this->phpExcel, 'Excel5');
        $response = $this->phpExcelService->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'gantt_chart.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}