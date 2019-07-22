<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 10.04.19
 * Time: 10:54
 */

namespace InfrastructureBundle\Service\Export;

use InfrastructureBundle\Entity\Computer;
use InfrastructureBundle\Entity\ComputerPart;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet_PageSetup;

class InfrastructureBuilder
{
    /**
     * @var \PHPExcel
     */
    protected $phpExcel;

    /**
     * InfrastructureBuilder constructor.
     * @param $phpExcel
     * @param $tranlsator
     */
    public function __construct($phpExcel, $translator)
    {
        $this->phpExcel = $phpExcel->createPHPExcelObject();
        $this->translator = $translator;
    }

    /**
     * @param $computers
     * @param $exportType
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function build($computers, $exportType)
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

        $borderStyle = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $this->phpExcel->getDefaultStyle()->applyFromArray($styleArray);

        $this->phpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
        $this->phpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('E')->setWidth($exportType == 'computers' ? 35 : 20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->phpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->phpExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
        $this->phpExcel->getActiveSheet()->getColumnDimension('L')->setWidth($exportType == 'computers' ? 40 : 25 );
        
        if ($exportType == 'computers') {
            $this->phpExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);
            $this->phpExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $this->phpExcel->getActiveSheet()->getColumnDimension('O')->setWidth(50);
            $this->phpExcel->getActiveSheet()->getColumnDimension('P')->setWidth(50);
            $this->phpExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $this->phpExcel->getActiveSheet()->getColumnDimension('R')->setWidth(50);
            $this->phpExcel->getActiveSheet()->getColumnDimension('S')->setWidth(50);
            $this->phpExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $this->phpExcel->getActiveSheet()->getColumnDimension('U')->setWidth(35);
            $this->phpExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
            $this->phpExcel->getActiveSheet()->getColumnDimension('W')->setWidth(35);
            $this->phpExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $this->phpExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(35);
            $this->phpExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);

            $this->phpExcel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('O1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('Q1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('R1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('S1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('T1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('U1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('V1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('W1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('X1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('Y1')->getFont()->setBold(true);
            $this->phpExcel->getActiveSheet()->getStyle('Z1')->getFont()->setBold(true);
        }

        $this->phpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
        $this->phpExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);

        if ($exportType != 'computerParts') {
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('A1', $this->translator->trans('IP Address'));
            $this->phpExcel->getActiveSheet()->mergeCells('A1:A2');

            $this->phpExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('B1', $this->translator->trans('Way to get the ip-address'));
            $this->phpExcel->getActiveSheet()->mergeCells('B1:B2');

            $this->phpExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('C1', $this->translator->trans('MAC Address'));
            $this->phpExcel->getActiveSheet()->mergeCells('C1:C2');

        }
        $currentRow = 3;

        if ($exportType == 'computers') {

            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $this->translator->trans('PC name'));
            $this->phpExcel->getActiveSheet()->mergeCells('D1:D2');

            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E1', $this->translator->trans('Employee'));
            $this->phpExcel->getActiveSheet()->mergeCells('E1:E2');

            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F1', $this->translator->trans('Room'));
            $this->phpExcel->getActiveSheet()->mergeCells('F1:F2');

            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('G1', $this->translator->trans('Type'));
            $this->phpExcel->getActiveSheet()->mergeCells('G1:G2');

            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('H1', $this->translator->trans('Model'));
            $this->phpExcel->getActiveSheet()->mergeCells('H1:H2');

            $this->phpExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('I1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('I1:I2');

            $this->phpExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('J1', $this->translator->trans('Domain'));
            $this->phpExcel->getActiveSheet()->mergeCells('J1:J2');

            $this->phpExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('K1', $this->translator->trans('Operation system'));
            $this->phpExcel->getActiveSheet()->mergeCells('K1:K2');

            $this->phpExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('L1', $this->translator->trans('Key OS in system'));
            $this->phpExcel->getActiveSheet()->mergeCells('L1:L2');

            $this->phpExcel->getActiveSheet()->getStyle('M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('M1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('M1', $this->translator->trans('Key OS on sticker'));
            $this->phpExcel->getActiveSheet()->mergeCells('M1:M2');

            $this->phpExcel->getActiveSheet()->getStyle('N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('N1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('N1', $this->translator->trans('Legal'));
            $this->phpExcel->getActiveSheet()->mergeCells('N1:N2');

            $this->phpExcel->getActiveSheet()->getStyle('O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('O1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('O1', $this->translator->trans('Processor'));
            $this->phpExcel->getActiveSheet()->mergeCells('O1:O2');

            $this->phpExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('P1', $this->translator->trans('Video card'));
            $this->phpExcel->getActiveSheet()->mergeCells('P1:P2');

            $this->phpExcel->getActiveSheet()->getStyle('Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('Q1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('Q1', $this->translator->trans('RAM'));
            $this->phpExcel->getActiveSheet()->mergeCells('Q1:Q2');

            $this->phpExcel->getActiveSheet()->getStyle('R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('R1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('R1', $this->translator->trans('First HDD'));
            $this->phpExcel->getActiveSheet()->mergeCells('R1:R2');

            $this->phpExcel->getActiveSheet()->getStyle('S1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('S1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('S1', $this->translator->trans('Second HDD'));
            $this->phpExcel->getActiveSheet()->mergeCells('S1:S2');

            $this->phpExcel->getActiveSheet()->getStyle('T1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('T1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('T1', $this->translator->trans('Motherboard'));
            $this->phpExcel->getActiveSheet()->mergeCells('T1:T2');

            $this->phpExcel->getActiveSheet()->getStyle('U1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('U1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('U1', $this->translator->trans('monitor'));
            $this->phpExcel->getActiveSheet()->mergeCells('U1:U2');

            $this->phpExcel->getActiveSheet()->getStyle('V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('V1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('V1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('V1:V2');

            $this->phpExcel->getActiveSheet()->getStyle('W1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('W1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('W1', $this->translator->trans('keyboard'));
            $this->phpExcel->getActiveSheet()->mergeCells('W1:W2');

            $this->phpExcel->getActiveSheet()->getStyle('X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('X1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('X1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('X1:X2');

            $this->phpExcel->getActiveSheet()->getStyle('Y1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('Y1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('Y1', $this->translator->trans('mouse'));
            $this->phpExcel->getActiveSheet()->mergeCells('Y1:Y2');

            $this->phpExcel->getActiveSheet()->getStyle('Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('Z1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('Z1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('Z1:Z2');

            /** @var Computer $computer */
            foreach ($computers as $computer) {

                if (is_array($computer->getIpAddressComputer())) {
                    $computerIpAddress = str_replace(['"',']', '['], '', json_encode($computer->getIpAddressComputer()));
                } else {
                    $computerIpAddress = $computer->getIpAddressComputer();
                }

                if (is_array($computer->getMacAddressComputer())) {
                    $computerMacAddress = str_replace(['"',']', '['], '', json_encode($computer->getMacAddressComputer()));
                } else {
                    $computerMacAddress = $computer->getMacAddressComputer();
                }

                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $computerIpAddress);

                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $computer->getIpType());


                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $computerMacAddress);

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $computer->getName());

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $computer->getEmployee() ? $computer->getEmployee()->getFullname(true) : '-');

                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $computer->getEmployee() ? $computer->getEmployee()->getRoom() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $this->translator->trans($computer->getType()));

                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $computer->getModel());

                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $computer->getInventoryNumber() ? $computer->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $computer->getDomain() ? $computer->getDomain() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $computer->getOperationSystem() ? $computer->getOperationSystem()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $computer->getKeyInSystem() ? $computer->getKeyInSystem() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('M' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('M' . $currentRow, $computer->getKeyOnSticker() ? $computer->getKeyOnSticker() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('N' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('N' . $currentRow, $computer->isLegal() ? $this->translator->trans('Yes') : $this->translator->trans('No'));

                $this->phpExcel->getActiveSheet()->getStyle('O' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('O' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('O' . $currentRow, $computer->getProcessor() ? $computer->getProcessor()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('P' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('P' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('P' . $currentRow, $computer->getVideoCard() ? $computer->getVideoCard()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('Q' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('Q' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('Q' . $currentRow, $computer->getRam() ? $computer->getRam()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('R' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('R' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('R' . $currentRow, $computer->getHddFirst() ? $computer->getHddFirst()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('S' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('S' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('S' . $currentRow, $computer->getHddSecond() ? $computer->getHddSecond()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('T' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('T' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('T' . $currentRow, $computer->getMotherboard() ? $computer->getMotherboard()->getName() : '-');

                $monitorRow = $currentRow;

                foreach ($computer->getTiedComputerPartsName() as $monitor) {

                    $this->phpExcel->getActiveSheet()->getStyle('U' . $monitorRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->phpExcel->getActiveSheet()->getStyle('U' . $monitorRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('U' . $monitorRow, $monitor['name']);

                    $this->phpExcel->getActiveSheet()->getStyle('V' . $monitorRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->phpExcel->getActiveSheet()->getStyle('V' . $monitorRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $this->phpExcel->getActiveSheet()->setCellValue('V' . $monitorRow, $monitor['inventoryNumber']);

                    $monitorRow++;
                }

                $this->phpExcel->getActiveSheet()->getStyle('W' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('W' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('W' . $currentRow, $computer->getKeyboard() ? $computer->getKeyboard()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('X' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('X' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('X' . $currentRow, $computer->getKeyboard() ? $computer->getKeyboard()->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('Y' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('Y' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('Y' . $currentRow, $computer->getMouse() ? $computer->getMouse()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('Z' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('Z' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('Z' . $currentRow, $computer->getMouse() ? $computer->getMouse()->getInventoryNumber() : '-');

                $currentRow++;

                $currentRow = $currentRow > $monitorRow ? $currentRow : $monitorRow;

                $this->phpExcel->getActiveSheet()->getStyle('A1:Z' . ($currentRow - 1))->applyFromArray($borderStyle);
            }
        }

        if ($exportType == 'servers') {
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $this->translator->trans('Server type'));
            $this->phpExcel->getActiveSheet()->mergeCells('D1:D2');

            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E1', $this->translator->trans('Host'));
            $this->phpExcel->getActiveSheet()->mergeCells('E1:E2');

            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F1', $this->translator->trans('Domain'));
            $this->phpExcel->getActiveSheet()->mergeCells('F1:F2');

            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('H1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('H1:H2');

            $this->phpExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('I1', $this->translator->trans('Serial number'));
            $this->phpExcel->getActiveSheet()->mergeCells('I1:I2');

            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('G1', $this->translator->trans('Operation system'));
            $this->phpExcel->getActiveSheet()->mergeCells('G1:G2');

            $this->phpExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('J1', $this->translator->trans('Legal'));
            $this->phpExcel->getActiveSheet()->mergeCells('J1:J2');

            $this->phpExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('K1', $this->translator->trans('Installed service'));
            $this->phpExcel->getActiveSheet()->mergeCells('K1:K2');

            $this->phpExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('L1', $this->translator->trans('Room'));
            $this->phpExcel->getActiveSheet()->mergeCells('L1:L2');

            foreach ($computers as $computer) {
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $computer->getIpAddress());

                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $computer->getIpType());

                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $computer->getMacAddress());

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $computer->getType() ? $computer->getType() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $computer->getHost() ? $computer->getHost() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $computer->getDomain() ? $computer->getDomain() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $computer->getInventoryNumber() ? $computer->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $computer->getSerialNumber() ? $computer->getSerialNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('I' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('I' . $currentRow, $computer->getOperationSystem() ? $computer->getOperationSystem()->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('J' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('J' . $currentRow, $computer->isLegal() ? $this->translator->trans('Yes') : $this->translator->trans('No'));

                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('K' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('K' . $currentRow, $computer->getInstalledService() ? $computer->getInstalledService() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('L' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('L' . $currentRow, $computer->getRoom() ? $computer->getRoom() : '-');

                $currentRow++;
            }
            $this->phpExcel->getActiveSheet()->getStyle('A1:L' . ($currentRow - 1))->applyFromArray($borderStyle);
        }

        if ($exportType == 'printers') {
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $this->translator->trans('Model'));
            $this->phpExcel->getActiveSheet()->mergeCells('D1:D2');

            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E1', $this->translator->trans('Type cartridge'));
            $this->phpExcel->getActiveSheet()->mergeCells('E1:E2');

            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F1', $this->translator->trans('Quantity'));
            $this->phpExcel->getActiveSheet()->mergeCells('F1:F2');

            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('G1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('G1:G2');

            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('H1', $this->translator->trans('Room'));
            $this->phpExcel->getActiveSheet()->mergeCells('H1:H2');

            foreach ($computers as $computer) {
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $computer->getIpAddress());

                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $computer->getIpType());

                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $computer->getMacAddress());

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $computer->getModel() ? $computer->getModel() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $computer->getCartridgeType() ? $computer->getCartridgeType() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $computer->getQuantity() ? $computer->getQuantity() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('G' . $currentRow, $computer->getInventoryNumber() ? $computer->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('H' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('H' . $currentRow, $computer->getRoom() ? $computer->getRoom() : '-');

                $currentRow++;
            }
            $this->phpExcel->getActiveSheet()->getStyle('A1:H' . ($currentRow - 1))->applyFromArray($borderStyle);
        }

        if ($exportType == 'commutators') {
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $this->translator->trans('Model'));
            $this->phpExcel->getActiveSheet()->mergeCells('D1:D2');

            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('E1:E2');

            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('F1', $this->translator->trans('Room'));
            $this->phpExcel->getActiveSheet()->mergeCells('F1:F2');

            foreach ($computers as $computer) {
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $computer->getIpAddress());

                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $computer->getIpType());

                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $computer->getMacAddress());

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $computer->getModel() ? $computer->getModel() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $computer->getInventoryNumber() ? $computer->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('F' . $currentRow, $computer->getRoom() ? $computer->getRoom() : '-');

                $currentRow++;
            }
            $this->phpExcel->getActiveSheet()->getStyle('A1:F' . ($currentRow - 1))->applyFromArray($borderStyle);
        }

        if ($exportType == 'computer-parts') {
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('A1', $this->translator->trans('Type'));
            $this->phpExcel->getActiveSheet()->mergeCells('A1:A2');

            $this->phpExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('B1', $this->translator->trans('Name'));
            $this->phpExcel->getActiveSheet()->mergeCells('B1:B2');

            $this->phpExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('C1', $this->translator->trans('Inventory number'));
            $this->phpExcel->getActiveSheet()->mergeCells('C1:C2');

            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('D1', $this->translator->trans('Serial number'));
            $this->phpExcel->getActiveSheet()->mergeCells('D1:D2');

            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->phpExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->phpExcel->getActiveSheet()->setCellValue('E1', $this->translator->trans('Description'));
            $this->phpExcel->getActiveSheet()->mergeCells('E1:E2');

            /** @var ComputerPart $computerPart */
            foreach ($computers as $computerPart) {
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('A' . $currentRow, $this->translator->trans($computerPart->getType()));

                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('B' . $currentRow, $computerPart->getName() ? $computerPart->getName() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('C' . $currentRow, $computerPart->getInventoryNumber() ? $computerPart->getInventoryNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('D' . $currentRow, $computerPart->getSerialNumber() ? $computerPart->getSerialNumber() : '-');

                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->phpExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->phpExcel->getActiveSheet()->setCellValue('E' . $currentRow, $computerPart->getDescription() ? $computerPart->getDescription() : '-');

                $currentRow++;
            }
            $this->phpExcel->getActiveSheet()->getStyle('A1:E' . ($currentRow - 1))->applyFromArray($borderStyle);
        }

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
            ->setSubject("Выгрузка по инфраструктуре Олимпа")
            ->setDescription("Выгрузка инфраструктуры Олимпа")
        ;

        $this->phpExcel
            ->getActiveSheet()
            ->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    }
}
