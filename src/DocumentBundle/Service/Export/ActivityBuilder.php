<?php

namespace DocumentBundle\Service\Export;

use DocumentBundle\Entity\Activity;
use DocumentBundle\Entity\ActivityEvents;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet_PageSetup;

class ActivityBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * ActivityBuilder constructor.
     * @param $phpExcel
     * @param $tranlsator
     */
    public function __construct($phpExcel, $tranlsator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $tranlsator;
    }

    /**
     * @param $currentProjectsActivities
     * @param $preContractualProjects
     * @param $otherActivities
     * @param $deferredProjectsActivities
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($currentProjectsActivities, $preContractualProjects, $otherActivities, $deferredProjectsActivities)
    {
        $this->fillMeta();
        $this->phpExcel->setActiveSheetIndex(0);
        $styleArray = [
            'font' => [
                'size' => 10,
                'name' => 'Times new roman'
            ],
            'alignment' => [
                'wrap' => true
            ]
        ];

        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

        $this->phpExcel->getActiveSheet()->getStyle('A2')->getFont()
            ->setBold(true)
            ->setSize(16)
        ;
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->setCellValue('A2', 'Карта активностей НПО "Андроидная техника"');
        $this->phpExcel->getActiveSheet()->mergeCells('A2:M2');

        $date = new \DateTime();
        $this->phpExcel->getActiveSheet()->setCellValue('A4', "Дата:");
        $this->phpExcel->getActiveSheet()->setCellValue('C4',  $date->format('d.m.Y'));

        $this->phpExcel->getActiveSheet()->setCellValue('A6',  "№ п/п");
        $this->phpExcel->getActiveSheet()->mergeCells('A6:A7');
        $this->phpExcel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('B6', $this->translator->trans('Engineering code'));
        $this->phpExcel->getActiveSheet()->mergeCells('B6:B7');
        $this->phpExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('C6', $this->translator->trans('Activity'));
        $this->phpExcel->getActiveSheet()->mergeCells('C6:C7');
        $this->phpExcel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('D6', $this->translator->trans('Availability TEO, profitability') . ' %');
        $this->phpExcel->getActiveSheet()->mergeCells('D6:D7');
        $this->phpExcel->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('E6', "Сумма тыс, руб. с НДС");
        $this->phpExcel->getActiveSheet()->getStyle('E6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getRowDimension(6)->setRowHeight(15);
        $this->phpExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(25);
        $this->phpExcel->getActiveSheet()->mergeCells('E6:H6');

        $this->phpExcel->getActiveSheet()->setCellValue('E7', $this->translator->trans('Plan'));
        $this->phpExcel->getActiveSheet()->getStyle('E7')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('E7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('F7', $this->translator->trans('Fact'));
        $this->phpExcel->getActiveSheet()->getStyle('F7')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('F7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('G7', $this->translator->trans('Received'));
        $this->phpExcel->getActiveSheet()->getStyle('G7')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('G7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('H7', $this->translator->trans('Remaining amount'));
        $this->phpExcel->getActiveSheet()->getStyle('H7')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('H7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('I6', $this->translator->trans('Success events'));
        $this->phpExcel->getActiveSheet()->mergeCells('I6:I7');
        $this->phpExcel->getActiveSheet()->getStyle('I6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('I6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('J6', $this->translator->trans('Result'));
        $this->phpExcel->getActiveSheet()->mergeCells('J6:J7');
        $this->phpExcel->getActiveSheet()->getStyle('J6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('J6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('K6', $this->translator->trans('Additional events'));
        $this->phpExcel->getActiveSheet()->mergeCells('K6:K7');
        $this->phpExcel->getActiveSheet()->getStyle('K6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('K6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('L6', $this->translator->trans('End At'));
        $this->phpExcel->getActiveSheet()->mergeCells('L6:L7');
        $this->phpExcel->getActiveSheet()->getStyle('L6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('L6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('L6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('M6', $this->translator->trans('Responsible'));
        $this->phpExcel->getActiveSheet()->mergeCells('M6:M7');
        $this->phpExcel->getActiveSheet()->getStyle('M6')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('M6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('M6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $currentRow = 9;

        $this->phpExcel->getActiveSheet()->setCellValue('A8', 'I.');
        $this->phpExcel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('C8', $this->translator->trans('Current projects (under the concluded contracts)'));
        $this->phpExcel->getActiveSheet()->getStyle('C8')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $currentRow++;
        $currentRow = $this->groupByCategories($currentProjectsActivities, $currentRow);
        $currentRow--;

        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'II.');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans('Precontractual projects'));
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $currentRow++;
        $currentRow = $this->groupByCategories($preContractualProjects, $currentRow);
        $currentRow--;

        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'III.');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans('Other Activities'));
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $currentRow++;
        $currentRow = $this->groupByCategories($otherActivities, $currentRow);
        $currentRow--;

        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'IV.');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $this->translator->trans('Deferred projects (weekly changes)'));
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $currentRow++;
        $currentRow = $this->groupByCategories($deferredProjectsActivities, $currentRow);
        $currentRow--;

        $borderStyle = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $this->phpExcel->getActiveSheet()->getStyle('A6:M' . $currentRow)->applyFromArray($borderStyle);

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
            ->setSubject("Выгрузка карты активности")
            ->setDescription("Карта активности")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }

    /**
     * @param $activities
     * @param $currentRow
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    protected function groupByCategories($activities, $currentRow)
    {
        $count = 1;

        /** @var Activity $activity */
        foreach ($activities as $activity) {
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $count);
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $activity->getProject()->getCode());
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $activity->getActivity());
            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, number_format($activity->getProfitability(), 2) . '%');
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, number_format($activity->getPlan(), 2));
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, number_format($activity->getFact(), 2));
            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, number_format($activity->getReceived(), 2));
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, number_format($activity->getFact() - $activity->getReceived() >= 0 ? $activity->getFact() - $activity->getReceived() : 0, 2));
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $activity->getEndAt() ? $activity->getEndAt()->format('d.m.Y H:i') : '');
            $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $countActivityEvents = 1;
            foreach ($activity->getActivityEvents() as $activityEvent) {
                $date = !empty($activityEvent->getEndAt()) ? $activityEvent->getEndAt()->format('d.m.Y') : '-';
                $responsible = !empty($activityEvent->getResponsibleUser()) ? $activityEvent->getResponsibleUser()->getLastNameWithInitials() : '-';
                $text = '"' . $activityEvent->getName() . '" в срок до ' . '"' . $date . '"';
                if ($activityEvent->isSuccessEvent()) {
                    $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $countActivityEvents . '. ' . $text);
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $this->translator->trans($activityEvent->getStatusList()[$activityEvent->getStatus()]));
                    $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, $responsible);
                    $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    if ($activityEvent->getAdditionalEvents()) {
                        $countAdditionalEvents = 1;
                        /** @var ActivityEvents $additionalEvent */
                        foreach ($activityEvent->getAdditionalEvents() as $additionalEvent) {
                            $date = !empty($additionalEvent->getEndAt()) ? $additionalEvent->getEndAt()->format('d.m.Y') : '-';
                            $responsible = !empty($additionalEvent->getResponsibleUser()) ? $additionalEvent->getResponsibleUser()->getLastNameWithInitials() : '-';
                            $text = '"' . $additionalEvent->getName() . '" в срок до ' . '"' . $date . '" "' . $this->translator->trans($additionalEvent->getStatusList()[$activityEvent->getStatus()]) . '" "' . $responsible . '"' ;

                            $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $countAdditionalEvents . ' ' . $text);
                            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                            $countAdditionalEvents++;
                            $currentRow++;
                        }
                    }
                    $currentRow++;
                    $countActivityEvents++;
                }
            }
            $currentRow++;
            $count++;
        }
        return $currentRow;
    }
}