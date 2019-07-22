<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 19.02.19
 * Time: 15:23
 */

namespace AppBundle\Service\Export;

use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet_PageSetup;

class JobReportBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * JobReportBuilder constructor.
     * @param $phpExcel
     * @param $translator
     */
    public function __construct($phpExcel, $translator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
    }

    /**
     * @param Team $team
     * @param $tasks
     * @param User $user
     * @param $period
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build(Team $team, $tasks,User $user, $period)
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
        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        $headerRow = 3;

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $headerRow . ':I' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $headerRow, 'Утверждаю:');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $headerRow . ':I' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $headerRow, 'Генеральный директор');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $headerRow . ':I' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $headerRow, 'АО "НПО "Андроидная техника"');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $headerRow . ':I' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $headerRow, ' _________________ А.Ф. Пермяков');
        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(30);
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $headerRow . ':I' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $headerRow, '"_______"______________' . date('Y') . ' г.');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $headerRow, 'План/отчет о работе на ' . $period . ' года');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getStyle('H' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $headerRow, 'Подразделение (участок): ' . $team->getTitle());
        $headerRow += 3;
        $currentRow = $headerRow;
        $startRowTable = $currentRow;

        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, '№ п/п');

        $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, 'Проект');

        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, 'Бизнес-процессы, задачи');

        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, 'Срок выполнения (конечная дата)');

        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'Ответственный');

        $this->phpExcel->getActiveSheet()->mergeCells('F' . $currentRow . ':I' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, 'Отчет о выполнении');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('F' . $currentRow . ':G' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, 'план');

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $currentRow . ':I' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'факт');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, 'документ (изделие)/ %выполнения*');

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, 'норма, час');

        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'документ (изделие)/ %выполнения*');

        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, 'факт, час');
        $this->phpExcel->getActiveSheet()->mergeCells('A' . $startRowTable . ':A' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('B' . $startRowTable . ':B' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('C' . $startRowTable . ':C' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('D' . $startRowTable . ':D' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('E' . $startRowTable . ':E' . $currentRow);

        $currentRow++;
        $count = 1;

        $totalOriginEstimate = 0;
        $totalTimeSpent = 0;

        /** @var ProjectTask $task */
        foreach ($tasks as $task) {
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $count);

            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $task->getProject()->getName());

            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $task->getTitle());

            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $task->getEndAt()->format('d.m.Y'));

            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $task->getResponsibleUser()->getLastNameWithInitials());

            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $task->getResult() ? $task->getResult()->getName() : '-');

            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $this->prepareTimeString($task->getOriginalEstimate()));

            $attachments = $task->getResponsibleUserAttachments($task->getResponsibleUser());

            if (!empty($attachments)) {
                $fact = end($attachments);
            } else {
                $fact = $task->getResponsibleUserLastComment($task->getResponsibleUser());
            }

            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, htmlspecialchars_decode(trim(strip_tags($fact))) ? : '-');

            $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ;

            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $this->prepareTimeString($task->getTimeSpent()));

            $totalOriginEstimate += $task->getOriginalEstimate();
            $totalTimeSpent += $task->getTimeSpent();

            $count++;
            $currentRow++;

        }
        $currentRow--;
        $this->phpExcel->getActiveSheet()->getStyle('A' . $startRowTable . ':I' . $currentRow)->applyFromArray($allBordersStyle);

        $currentRow++;

        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, 'Итого');
        $this->phpExcel->getActiveSheet()->getStyle('F' . $startRowTable . ':I' . $currentRow)->applyFromArray($allBordersStyle);

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $this->prepareTimeString($totalOriginEstimate));

        $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $this->prepareTimeString($totalTimeSpent));

        $currentRow += 2;

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'Должность: ' . $user->getEmployeeRole()->getName());

        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $user->getLastNameWithInitials());

        return $this->phpExcel;
    }

    /**
     * @throws \PHPExcel_Exception
     */
    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("План отчет о работе")
            ->setDescription("План отчет о работе")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
        ;
    }

    /**
     * @param float $time
     * @return string
     */
    protected function prepareTimeString($time)
    {
        $timeSpent = round($time, 2);
        $spentHours = floor($timeSpent);
        $spentMinutes = round(($timeSpent - $spentHours) * 60);

       return  $spentHours . 'ч' . ($spentMinutes ? ' ' . $spentMinutes . 'м' : '');
    }
}