<?php

namespace AppBundle\Service\Export;


use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\ProtocolMembers;
use AppBundle\Utils\DateUtils;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class ProtocolBuilder
{
    /**
     * @param $task
     * @return PhpWord
     */
    public function build($task)
    {
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        /** @var ProjectTask $task */
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($task->getResponsibleUser());
        $properties->setCompany('Андроидная техника');
        $properties->setLastModifiedBy('Olymp');
        $properties->setTitle('Выгрузка');
        $properties->setDescription('Отчет по протоколу');
        $properties->setSubject('Отчет по протоколу');

        $section = $phpWord->addSection();

        $header = $section->addHeader();
        $imageStyle = [
            'width' => 500,
            'align' => 'center'
        ];
        $header->addImage('images/colontitles/headerAT.png', $imageStyle);

        $footer = $section->addFooter();
        $imageStyle = [
            'width' => 300,
            'align' => 'center'
        ];
        $footer->addImage('images/colontitles/footerAT.png', $imageStyle);

        $title = 'Протокол №' . $task->getTitle();
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $month = DateUtils::transliterate($task->getStartAt()->format('F'));

        $text = '«' . $task->getStartAt()->format('d') . '» ' . $month . $task->getStartAt()->format(' Y г.');
        $section->addText(htmlspecialchars_decode(trim(strip_tags($text))), [], ['alignment' => 'right']);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(4000)->addText($task->getSubject(), ['bold' => true]);

        $text = str_replace('<br>', '<br/>', $task->getDescription());
        Html::addHtml($section, $text);
        $section->addTextBreak();

        $text = [];
        /** @var ProtocolMembers $member */
        foreach ($task->getProtocolMembers() as $member) {
            $text[] = $member->getMember()->getLastNameWithInitials();
        }
        $section->addText('Присутствовали: ' . ' ' . join(', ', $text));

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');
        $table->addRow(null, ['tblHeader' => true,]);

        $table->addCell(600, $cellVCentered)->addText('№ п/п', ['bold' => true, 'size' => 10], $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Принятое решение', ['bold' => true, 'size' => 10], $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('Срок', ['bold' => true, 'size' => 10], $cellHCentered);
        $table->addCell(1900, $cellVCentered)->addText('Ответственный', ['bold' => true, 'size' => 10], $cellHCentered);

        $table->addCell(1500, $cellVCentered)->addText('Результат', ['bold' => true, 'size' => 10], $cellHCentered);
        $table->addCell(1400, $cellVCentered)->addText('Отметка о выполнении', ['bold' => true, 'size' => 10], $cellHCentered);

        $currentRow = 1;
        $siblings = [];

        /** @var ProjectTask $protocolTask */
        foreach ($task->getProtocolTasks() as $protocolTask) {

            if (!empty($protocolTask->getTaskSiblings()) &&
                in_array($protocolTask->getTaskSiblings()->getId(), $siblings)) {
                continue;
            }

            $table->addRow();

            $table->addCell(600, $cellVCentered)->addText($currentRow . '.', ['size' => 10], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText($protocolTask->getTitle(), ['size' => 10], []);
            $table->addCell(1500, $cellVCentered)->addText($protocolTask->getEndAt()->format('d.m.Y'), ['size' => 10], $cellHCentered);

            $responsible = [
                $protocolTask->getResponsibleUser()->getId() =>
                    $protocolTask->getResponsibleUser()->getLastNameWithInitials()
            ];

            if (!empty($protocolTask->getTaskSiblings())) {
                $siblings[] = $protocolTask->getTaskSiblings()->getId();

                foreach ($protocolTask->getTaskSiblings()->getSiblings() as $sibling) {
                    $responsible[$sibling->getResponsibleUser()->getId()] =
                        $sibling->getResponsibleUser()->getLastNameWithInitials();
                }
            }

            $table->addCell(1900, $cellVCentered)->addText(join("\n", $responsible), ['size' => 10], $cellHCentered);

            $table->addCell(1500, $cellVCentered)->addText(htmlspecialchars_decode($protocolTask->getResult() ? $protocolTask->getResult() : '-'), ['size' => 10], $cellHCentered);
            $table->addCell(1400, $cellVCentered)->addText('', ['size' => 10], $cellHCentered);

            $currentRow++;
        }

        $section->addTextBreak();
        $tab = '		';
        $underlining = '_______________';

        $text = 'Руководитель совещания ' . $tab . $underlining . $tab . $task->getControllingUser()->getLastNameWithInitials();
        Html::addHtml($section, $text);
        $section->addText(htmlspecialchars_decode(trim(strip_tags('(подпись)'))), ['size' => 9], $cellHCentered);

        $text = 'Секретарь ' . $tab . $tab . $underlining . $tab . $task->getReporter()->getLastNameWithInitials();
        Html::addHtml($section, $text);
        $section->addText(htmlspecialchars_decode(trim(strip_tags('(подпись)'))), ['size' => 9], $cellHCentered);

        return $phpWord;
    }
}