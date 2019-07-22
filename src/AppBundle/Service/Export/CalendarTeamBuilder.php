<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\DayOff;
use AppBundle\Entity\Team;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet_PageSetup;

class CalendarTeamBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * MonthlyReportBuilder constructor.
     * @param $phpExcel
     */
    public function __construct($phpExcel, $translator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
    }

    /**
     * @param $team
     * @param $dayOffTeamMembers
     * @param $year
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($team, $dayOffTeamMembers, $year)
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

        $topBorderStyle = [
            'borders' => [
                'top' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $styleArray = [
            'font' => [
                'size' => 8,
                'name' => 'Verdana'
            ],
            'alignment' => [
                'wrap' => true
            ]
        ];
        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

        $headerRow = 1;

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $headerRow . ':M' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $headerRow, 'Унифицированная форма № Т-7');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $headerRow . ':M' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $headerRow, 'Утверждена постановлением Госкомстата');
        $headerRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $headerRow . ':M' . $headerRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $headerRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $headerRow, 'России от 05.01.2004 № 1');

        $currentRow = 5;

        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $this->translator->trans('Code'));
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'Форма по ОКУД');

        $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, '301020');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':J' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'НПО "Андроидная техника"');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()
            ->setBold(true)
            ->setSize(9)
        ;

        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'по ОКПО');

        $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, '64412177');
        $this->phpExcel->getActiveSheet()->getStyle('L5:M7')->applyFromArray($allBordersStyle);
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':J' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'наименование организации');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getFont()->setSize(6);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($topBorderStyle);

        $currentRow = 10;

        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, 'УТВЕРЖДАЮ');
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getFont()
            ->setBold(true)
            ->setSize(9)
        ;
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'Мнение выборного профсоюзного органа');

        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, 'Руководитель');
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':M' . $currentRow);
        /** @var Team $team */
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $team->getLeader()->getEmployeeRole());
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(25);
        $this->phpExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':C' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'от "_____" _____________ 20_____ года № _____ учтено');

        $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, 'Номер документа');

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, 'Дата составления');

        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'На год');

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'должность');
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getFont()->setSize(6);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow . ':M' . $currentRow)->applyFromArray($topBorderStyle);
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('C' . $currentRow . ':E' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, 'ГРАФИК ОТПУСКОВ');
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()
            ->setBold(true)
            ->setSize(12)
        ;

        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $date = new \DateTime();
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $date->format('d.m.Y'));

        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $year);
        $this->phpExcel->getActiveSheet()->getStyle('F12:H13')->applyFromArray($allBordersStyle);

        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $team->getLeader()->getLastNameWithInitials());
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow . ':M' . $currentRow)->applyFromArray($topBorderStyle);
        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, 'личная подпись');
        $this->phpExcel->getActiveSheet()->mergeCells('L' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, 'расшифровка подписи');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, '"____" ____________ 20____ г.');

        $currentRow = 18;

        $this->phpExcel->getActiveSheet()->mergeCells('A18:A20');
        $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, 'Структурное подразделение');

        $this->phpExcel->getActiveSheet()->mergeCells('B18:B20');
        $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, 'Должность (специальность, профессия) по штатному расписанию');

        $this->phpExcel->getActiveSheet()->mergeCells('C18:C20');
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, 'Фамилия, имя, отчество');

        $this->phpExcel->getActiveSheet()->mergeCells('D18:D20');
        $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, 'Табельный номер');

        $this->phpExcel->getActiveSheet()->mergeCells('M18:M20');
        $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, 'Примечание');

        $this->phpExcel->getActiveSheet()->mergeCells('E18:L18');
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'ОТПУСК');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->mergeCells('E19:F20');
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'Количество календарных дней');

        $this->phpExcel->getActiveSheet()->mergeCells('G19:I19');
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, 'Дата');

        $this->phpExcel->getActiveSheet()->mergeCells('J19:L19');
        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, 'Перенесение отпуска');
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(25);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, 'Запланированная');

        $this->phpExcel->getActiveSheet()->mergeCells('H20:I20');
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'Фактическая');

        $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, 'Основание (документ)');

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':L' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'Дата предполагаемого отпуска');
        $currentRow++;

        /** @var DayOff $dayOffTeamMember */
        foreach ($dayOffTeamMembers as $dayOffTeamMember) {
            $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(40);
            $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $dayOffTeamMember->getOwner()->getTeam()->getTitle());
            $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ;

            $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $dayOffTeamMember->getOwner()->getEmployeeRole());
            $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ;

            $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $dayOffTeamMember->getOwner()->getFullName(true));
            $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ;

            $days = ceil(abs(strtotime($dayOffTeamMember->getDateEnd()->format('Y-m-d H:i'))-strtotime($dayOffTeamMember->getDateStart()->format('Y-m-d H:i')))/(60*60*24));

            $this->phpExcel->getActiveSheet()->mergeCells('E' . $currentRow . ':F' . $currentRow);
            $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $days);
            $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ;

            $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $dayOffTeamMember->getDateStart()->format('d.m.Y'));
            $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ;

            $this->phpExcel->getActiveSheet()->mergeCells('H' . $currentRow . ':I' . $currentRow);

            $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':L' . $currentRow);

            $currentRow++;
        }

        $currentRow--;

        $this->phpExcel->getActiveSheet()->getStyle('A18:M' . $currentRow)->applyFromArray($allBordersStyle);

        $currentRow += 2;

        $this->phpExcel->getActiveSheet()->mergeCells('C' . $currentRow . ':D' . $currentRow);
        $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, 'Руководитель кадровой службы');
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getFont()
            ->setBold(true)
            ->setSize(9)
        ;
        $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;

        $this->phpExcel->getActiveSheet()->mergeCells('E' . $currentRow . ':G' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('H' . $currentRow . ':J' . $currentRow);
        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':M' . $currentRow);
        $currentRow++;

        $this->phpExcel->getActiveSheet()->getRowDimension($currentRow)->setRowHeight(20);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow . ':M' . $currentRow)->applyFromArray($topBorderStyle);

        $this->phpExcel->getActiveSheet()->mergeCells('E' . $currentRow . ':G' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, 'должность');
        $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getFont()->setSize(6);

        $this->phpExcel->getActiveSheet()->mergeCells('H' . $currentRow . ':J' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, 'личная подпись');
        $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getFont()->setSize(6);

        $this->phpExcel->getActiveSheet()->mergeCells('K' . $currentRow . ':M' . $currentRow);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, 'расшифровка подписи');
        $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getFont()->setSize(6);

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
            ->setSubject("Календарь отпусков")
            ->setDescription("Календарь отпусков")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
        ;
    }
}