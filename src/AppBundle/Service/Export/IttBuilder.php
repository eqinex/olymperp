<?php

namespace AppBundle\Service\Export;

use AppBundle\Entity\Project;
use AppBundle\Entity\Specification;
use AppBundle\Entity\User;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class IttBuilder
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
     * @param $user
     * @param $project
     * @param $ittTechnical
     * @param $ittFunctional
     * @return PhpWord
     */
    public function build($user, $project, $ittTechnical, $ittFunctional)
    {
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $section = $phpWord->addSection([
            'orientation' => 'landscape'
        ]);

        /** @var Project $project */
        $table = $section->addTable();
        $table->addRow();

        $table->addCell(10000)->addText('СОГЛАСОВАНО' . '<w:br/>' . $this->translator->trans('Technical director') . '<w:br/>' . 'НПО «Анроидная техника»' . '<w:br/><w:br/>'
            . '______________Е.А. Дудоров' . '<w:br/>' . '«___»_______________ 20__ г.');
        $table->addCell(4000)->addText('УТВЕРЖДАЮ' . '<w:br/>' . $this->translator->trans('General Manager')  . '<w:br/>' . 'НПО «Анроидная техника»' . '<w:br/><w:br/>'
            . '______________А.Ф. Пермяков' . '<w:br/>' . '«___»_______________ 20__ г.');

        $section->addTextBreak();

        $title = 'Чек-лист по ВТЗ';
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];
        $cellColSpan = ['gridSpan' => 8, 'valign' => 'center'];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');
        $table->addRow(null, ['tblHeader' => true,]);

        $table->addCell(600, $cellVCentered)->addText('№ п/п', ['bold' => true], $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('Проект', ['bold' => true], $cellHCentered);
        $table->addCell(4500, $cellVCentered)->addText('Характеристика/параметр', ['bold' => true], $cellHCentered);
        $table->addCell(600, $cellVCentered)->addText('Ед. изм.', ['bold' => true], $cellHCentered);
        $table->addCell(1500, $cellVCentered)-> addText('ТЗ', ['bold' => true], $cellHCentered);
        $table->addCell(1500, $cellVCentered)-> addText('ВТЗ', ['bold' => true], $cellHCentered);
        $table->addCell(1500, $cellVCentered)-> addText('Отличия ТЗ от ВТЗ', ['bold' => true], $cellHCentered);
        $table->addCell(2300, $cellVCentered)->addText('Примечание', ['bold' => true], $cellHCentered);

        $table->addRow();
        $table->addCell(2300, $cellColSpan)->addText('Функциональные характеристики', ['bold' => true]);
        $currentRow = 1;
        /** @var Specification $itt */
        foreach ($ittFunctional as $itt) {
            $table->addRow();

            $table->addCell(600, $cellVCentered)->addText($currentRow, [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)->addText($project->getName(), [], $cellHCentered);
            $table->addCell(4500, $cellVCentered)->addText($itt->getWare()->getName(), [], $cellHCentered);
            $table->addCell(600, $cellVCentered)->addText($itt->getUnit(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getValueTask(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getValueInnerTask(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getDifference(), [], $cellHCentered);
            $table->addCell(2300, $cellVCentered)->addText($itt->getNotice(), [], $cellHCentered);

            $currentRow++;
        }

        $table->addRow();
        $table->addCell(2300, $cellColSpan)->addText('Технические характеристики', ['bold' => true]);

        foreach ($ittTechnical as $itt) {
            $table->addRow();

            $table->addCell(600, $cellVCentered)->addText($currentRow, [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)->addText($project->getName(), [], $cellHCentered);
            $table->addCell(4500, $cellVCentered)->addText($itt->getWare()->getName(), [], $cellHCentered);
            $table->addCell(600, $cellVCentered)->addText($itt->getUnit(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getValueTask(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getValueInnerTask(), [], $cellHCentered);
            $table->addCell(1500, $cellVCentered)-> addText($itt->getDifference(), [], $cellHCentered);
            $table->addCell(2300, $cellVCentered)->addText($itt->getNotice(), [], $cellHCentered);

            $currentRow++;
        }

        $section->addTextBreak();

        $miniTab = '	';
        $tab = '						';
        $underlining = '_____________________';

        $section->addText('Составил:', ['bold' => true]);

        /** @var User $user */
        $text = $user->getEmployeeRole()->getName() . $miniTab . $tab . $underlining . $tab . $user->getLastNameWithInitials();
        Html::addHtml($section, $text);
        $section->addText(htmlspecialchars_decode(trim(strip_tags('(подпись, дата)'))), ['size' => 9], $cellHCentered);

        return $phpWord;
    }
}