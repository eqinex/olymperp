<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\ProjectTask;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet_PageSetup;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskStatsBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     * @param $translator
     * @param $router
     */
    public function __construct($phpExcel, $translator, $router)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param $tasks
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($tasks)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);

        $headerRow = 1;
        $currentRow = 3;
        
        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(40);
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(20)
        ;
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A1', "Отчет по задачам");
        $this->phpExcel->getActiveSheet()
            ->mergeCells('A1:H1');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($headerRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->setCellValue('A2', "Месяц");
        $this->phpExcel->getActiveSheet()->setCellValue('B2', "№ П/П");
        $this->phpExcel->getActiveSheet()->setCellValue('C2', "Статус");
        $this->phpExcel->getActiveSheet()->setCellValue('D2', "Задача");
        $this->phpExcel->getActiveSheet()->setCellValue('E2', "Решение");
        $this->phpExcel->getActiveSheet()->setCellValue('F2', "Ответственный");
        $this->phpExcel->getActiveSheet()->setCellValue('G2', "Контроль");
        $this->phpExcel->getActiveSheet()->setCellValue('H2', "Шифр");
        $this->phpExcel->getActiveSheet()->setCellValue('I2', "Завершить до");
        $this->phpExcel->getActiveSheet()->setCellValue('J2', "№ Протокола");

        $stateColors = [
            ProjectTask::STATUS_NEW => '84b547',
            ProjectTask::STATUS_IN_PROGRESS => '2c97de',
            ProjectTask::STATUS_DONE => 'e76d3b',
            ProjectTask::STATUS_CANCELLED => 'cc3e4a',
            ProjectTask::STATUS_NEED_APPROVE => '2d2d2d',
            ProjectTask::STATUS_READY_TO_WORK => '84b547',
            ProjectTask::STATUS_ON_HOLD => 'e76d3b',
            ProjectTask::STATUS_NEED_APPROVE_RESULT=> '2d2d2d',
        ];
        $strikeThrough = [ProjectTask::STATUS_DONE, ProjectTask::STATUS_CANCELLED];

        /** @var ProjectTask $task */
        foreach ($tasks as $task) {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
            $this->phpExcel->getActiveSheet()->setCellValue(
                'A' . $currentRow,
                $this->translator->trans($task->getEndAt()->format('F'))
            );
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $task->getId());
            $path =  $this->router->generate('project_task_details', ['id' => $task->getProject()->getId(), 'taskId' => $task->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->phpExcel->getActiveSheet()->getCell('B' . $currentRow)->getHyperlink()->setUrl($path);
            $this->phpExcel->getActiveSheet()->getCell('B' . $currentRow)->getStyle()->getFont()->setColor(new \PHPExcel_Style_Color('FF0000FF'));

            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()->setColor(
                new \PHPExcel_Style_Color('FF' . $stateColors[$task->getStatus()])
            );

            if (in_array($task->getStatus(), $strikeThrough)) {
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getFont()->setStrikethrough(
                    true
                );
            }

            $responsibleUserAttachments = implode($task->getResponsibleUserAttachments($task->getResponsibleUser()), ', ');
            $responsibleUserLastComment = strip_tags($task->getResponsibleUserLastComment($task->getResponsibleUser()));

            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans("status_" . $task->getStatusList()[$task->getStatus()]));
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $task->getTitle());

            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setWrapText(true);
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(-1);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $responsibleUserAttachments . ' ' . $responsibleUserLastComment);

            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $task->getResponsibleUser() ? $task->getResponsibleUser()->getLastNameWithInitials() : '');
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $task->getControllingUser() ? $task->getControllingUser()->getLastNameWithInitials() : '');
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $task->getProject()->getCode());
            $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $task->getProtocol() ? $task->getProtocol()->getTitle() : '');

            if ($task->isActive() and $task->isExpired()) {
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getFont()->setColor(
                    new \PHPExcel_Style_Color('FFFF4500')
                );
            }
            $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $task->getEndAt()->format('d.m.Y'));

            $currentRow++;
        }
        $this->phpExcel->getActiveSheet()->setTitle('Отчет по задачам');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcel->setActiveSheetIndex(0);

        return $this->phpExcel;
    }

    protected function fillMeta()
    {
        $this->phpExcel->getProperties()->setCreator("Olymp")
            ->setLastModifiedBy("Olymp")
            ->setTitle("Выгрузка")
            ->setSubject("Отчет по задачам")
            ->setDescription("Отчет по задачам")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}