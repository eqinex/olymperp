<?php

namespace AppBundle\Service\Export;


use AppBundle\Entity\User;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;

class ReportTasksBuilder
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
     * @param $filters
     * @param $report
     * @param $customOrderBy
     * @param $customGroupBy
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($filters, $report, $customOrderBy, $customGroupBy)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $headerRow = 1;
        $currentRow = 3;
        $customFilter = '';
        $startAt = new \DateTime(date('01.m.Y'));
        $endAt = new \DateTime(date('t.m.Y'));
        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth($customGroupBy == 'person' ? 25 : 50);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(40);
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(9);

        if ($customOrderBy == 'executedOnTimeAndPercent') {
            $customFilter = $customGroupBy == 'team' ? '(по %+кол-ву выполненных в срок/отделу)' : ($customGroupBy == 'person' ? '(по %+кол-ву выполненных в срок/сотруднику)' : '(по %+кол-ву выполненных в срок/службе)');
            $this->phpExcel->getActiveSheet()->getStyle('F')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            $this->phpExcel->getActiveSheet()->getStyle('H')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            if ($customGroupBy == 'person') {
                $this->phpExcel->getActiveSheet()->getStyle('A3:I7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#FFFFFFCC');
            }
        } elseif ($customOrderBy == 'overdueTask') {
            $customFilter = $customGroupBy == 'team' ? '(по кол-ву просроченных задач/отделу)' : ($customGroupBy == 'person' ? '(по кол-ву просроченных задач в срок/сотруднику)' : '(по кол-ву просроченных задач в срок/службе)');
            $this->phpExcel->getActiveSheet()->getStyle('G')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            if ($customGroupBy == 'person') {
                $this->phpExcel->getActiveSheet()->getStyle('A3:I7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#FFFFFFCC');
            }
        } elseif ($customOrderBy == 'percentOverdueTask') {
            $customFilter = $customGroupBy == 'team' ? '(по % просроченных задач/отделу)' : ($customGroupBy == 'person' ? '(по % просроченных задач в срок/сотруднику)' : '(по % просроченных задач в срок/службе)');
            $this->phpExcel->getActiveSheet()->getStyle('H')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            if ($customGroupBy == 'person') {
                $this->phpExcel->getActiveSheet()->getStyle('A3:I7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#FFFFFFCC');
            }
        }
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A1', "Агрегированный отчет по исполнительной дисциплине, период " . (!empty($filters['startAt'])? $filters['startAt'] : $startAt->format('d.m.Y') . ' - ' . $endAt->format('d.m.Y')) . ' ' . $customFilter);
        $this->phpExcel->getActiveSheet()
            ->mergeCells('A1:I1');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(50);
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true)->setSize(10);
        $this->phpExcel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setWrapText(true);
        $this->phpExcel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A2', "№ П/П");
        $this->phpExcel->getActiveSheet()->setCellValue('B2', $customGroupBy != 'person' ? ($customGroupBy == 'team' ? 'Отдел' : 'Служба') : "Сотрудник");
        $this->phpExcel->getActiveSheet()->setCellValue('C2', "Всего");
        $this->phpExcel->getActiveSheet()->setCellValue('D2', "Завершено");
        $this->phpExcel->getActiveSheet()->setCellValue('E2', "Выполняется");
        $this->phpExcel->getActiveSheet()->setCellValue('F2', "В срок");
        $this->phpExcel->getActiveSheet()->setCellValue('G2', "Просрочено");
        $this->phpExcel->getActiveSheet()->setCellValue('H2', "% Выполненных в срок");
        $this->phpExcel->getActiveSheet()->setCellValue('I2', "Всего просрочено сейчас");

        if ($customOrderBy == 'percentOverdueTask') {
            $this->phpExcel->getActiveSheet()->setCellValue('H2', "% просроченных");
        } else {
            $this->phpExcel->getActiveSheet()->setCellValue('H2', "% Выполненных в срок");
        }



        $count = 1;

        foreach ($report as $reportTask) {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(25);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $count);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $reportTask['name']);
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, !empty($reportTask['totalTask']) ? $reportTask['totalTask'] : '0');
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, !empty($reportTask['completedTask']) ? $reportTask['completedTask'] : '0');
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, !empty($reportTask['performedTask']) ? $reportTask['performedTask'] : '0');
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, !empty($reportTask['completeOnTimeTask']) ? $reportTask['completeOnTimeTask'] : '0');
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, !empty($reportTask['overdueTask']) ? $reportTask['overdueTask'] : '0');
            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, !empty($reportTask['totalOverdueNowTask']) ? $reportTask['totalOverdueNowTask'] : '0');
            $this->phpExcel->getActiveSheet()->getStyle('C3:I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C3:I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $color = '84b547';

            if ($customOrderBy == 'percentOverdueTask') {
                $pexT = $reportTask['percentOverdueTask'];
                if ($pexT > 35) {
                    $color = 'cc3e4a';
                } else if ($pexT >= 15 && $pexT <= 35) {
                    $color = 'e76d3b';
                }

            } else {
                if ($reportTask['percentExecutedWorksTask'] < 50) {
                    $color = 'cc3e4a';
                } else if ($reportTask['percentExecutedWorksTask'] >= 50 && $reportTask['percentExecutedWorksTask'] <= 75) {
                    $color = 'e76d3b';
                }

                $pexT = $reportTask['percentExecutedWorksTask'];
            }

            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $pexT ? number_format($pexT,2) . '%' : '0.00%');

            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFont()->setColor(
                new \PHPExcel_Style_Color('FF' . $color)
            );
            $currentRow++;
            $count++;

        }

        $this->phpExcel->getActiveSheet()->setTitle('Агрегированный отчет');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcel->setActiveSheetIndex(0);

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Выгрузка агрегированного отчета")
            ->setDescription("Агрегированный отчет")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}