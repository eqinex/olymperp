<?php

namespace DocumentBundle\Service\Export;

use DocumentBundle\Entity\Document;
use DocumentBundle\Entity\DocumentSignatory;
use PhpOffice\PhpWord\PhpWord;

class DocumentApprovalSheetBuilder
{
    /**
     * DocumentCardBuilder constructor.
     * @param $tranlsator
     */
    public function __construct($tranlsator)
    {
        $this->translator = $tranlsator;
    }

    /**
     * @param $document
     * @return PhpWord
     */
    public function build($document)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        /** @var Document $document */
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($document->getOwner());
        $properties->setCompany('Андроидная техника');
        $properties->setLastModifiedBy('Olymp');
        $properties->setTitle('Выгрузка');
        $properties->setDescription('Лист согласования');
        $properties->setSubject('Лист согласования');

        $section = $phpWord->addSection();
        $header = $section->addHeader();

        $footer = $section->addFooter();
        $imageStyle = [
            'width' => 455,
            'align' => 'center'
        ];
        $footer->addImage('images/colontitles/footerAS.png', $imageStyle);

        $title = $this->translator->trans('Approval sheet');
        $section->addText(htmlspecialchars_decode(trim(strip_tags(mb_strtoupper($title)))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $text = 'к Договору № ' . $document->getCode() . ' от ' . $document->getCreatedAt()->format('d.m.Y') . ' года.';
        $section->addText(htmlspecialchars_decode(trim(strip_tags($text))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $section->addTextBreak();

        $styleTable = [
            'borderSize' => 2,
            'borderColor' => '999999',
        ];

        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Контрагент:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered)->addText($document->getSupplier()->getTitle(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Предмет договора:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered)->addText($document->getSubject(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered);
        $table->addCell(7000, $cellVCentered)->addText('(наименование товаров, работ, услуг)', ['superScript' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Способ оплаты:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered);
        $table->addCell(7000, $cellVCentered)->addText('(отсрочка на _ дней, предоплата в …%, 100% оплата и др.)', ['superScript' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Проект:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered)->addText($document->getProject()->getName(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Сумма договора:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered)->addText($document->getAmount(), ['italic' => true, 'size' => 11], $cellHCentered);

        $tab = '	';
        $doubleTab = $tab . $tab;

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Исполнитель:', ['bold' => true, 'size' => 11]);
        $table->addCell(7000, $cellVCentered)->addText($doubleTab . $tab . '/ ' . $tab . $document->getOwner()->getFullname(true), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered);
        $table->addCell(7000, $cellVCentered)->addText('(подпись, дата)' . $doubleTab . $doubleTab . '(ФИО)', ['superScript' => true, 'size' => 11], $cellHCentered);

        $section->addTextBreak();

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(500, $cellVCentered)->addText('№ п/п', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(1800, $cellVCentered)->addText('Согласующий отдел', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(1800, $cellVCentered)->addText('Согласовано / Не согласовано', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(1800, $cellVCentered)->addText('Замечания (причина не согласования)', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(1800, $cellVCentered)->addText('Дата', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(1800, $cellVCentered)->addText('Подпись / Расшифровка подписи', ['bold' => true, 'size' => 11], $cellHCentered);

        $currentRow = 1;

        /** @var DocumentSignatory $signatory */
        foreach ($document->getSignatories() as $signatory) {
            $table->addRow();
            $table->addCell(500, $cellVCentered)->addText($currentRow, ['size' => 11], $cellHCentered);
            $table->addCell(1800, $cellVCentered)->addText($signatory->getSignatory()->getTeam()->getTitle()
                . '<w:br/>' . $signatory->getSignatory()->getEmployeeRole()->getName()
                . '<w:br/>' . $signatory->getSignatory()->getLastNameWithInitials(), ['size' => 11], $cellHCentered);
            $table->addCell(1800, $cellVCentered)->addText('согласовано в СЭД', ['size' => 11], $cellHCentered);
            $table->addCell(1800, $cellVCentered);
            $table->addCell(1800, $cellVCentered);
            $table->addCell(1800, $cellVCentered);

            $currentRow++;
        }

        return $phpWord;
    }
}