<?php

namespace DocumentBundle\Service\Export;

use DocumentBundle\Entity\TechnicalMap;
use DocumentBundle\Entity\TechnicalMapSolutions;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class TechnicalMapBuilder
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
     * @param $technicalMap
     * @param $recommendedSolution
     * @return PhpWord
     */
    public function build($technicalMap, $recommendedSolution)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        /** @var TechnicalMap $technicalMap */
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($technicalMap->getOwner());
        $properties->setCompany('Андроидная техника');
        $properties->setLastModifiedBy('Olymp');
        $properties->setTitle('Выгрузка');
        $properties->setDescription($this->translator->trans('Technical solution maps'));
        $properties->setSubject($this->translator->trans('Technical solution maps'));

        $section = $phpWord->addSection([
            'orientation' => 'landscape'
        ]);

        $header = $section->addHeader();
        $footer = $section->addFooter();

        $table = $section->addTable();
        $table->addRow();

        $table->addCell(10000);
        $table->addCell(4000)->addText('УТВЕРЖДАЮ:' . '<w:br/>' . $this->translator->trans('General Manager')  . '<w:br/>' . 'АО НПО «Анроидная техника»' . '<w:br/><w:br/>'
            . '______________А.Ф. Пермяков' . '<w:br/>' . '«___»_______________ 20__ г.');

        $section->addTextBreak();

        $title = $this->translator->trans('Technical solution maps') . ' № ' . $technicalMap->getCode();
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $section->addTextBreak();

        $text = $this->translator->trans('Task') . ':   ' . $technicalMap->getTask();
        Html::addHtml($section, $text);

        $text = 'Цель решения задачи:   ' . $technicalMap->getGoal();
        Html::addHtml($section, $text);

        $text = 'Дата составления карты:   ' . $technicalMap->getCreatedAt()->format('d.m.Y');
        Html::addHtml($section, $text);

        $section->addTextBreak();

        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];
        $cellBgColor = ['valign' => 'center', 'bgcolor' => '#D7D7D7'];
        if ($technicalMap->getCriterionTitle4()) {
            $cellColSpan = ['gridSpan' => 4, 'valign' => 'center'];
        } elseif ($technicalMap->getCriterionTitle3()) {
            $cellColSpan = ['gridSpan' => 3, 'valign' => 'center'];
        } else {
            $cellColSpan = ['gridSpan' => 2, 'valign' => 'center'];
        }
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = ['vMerge' => 'continue', 'valign' => 'center'];

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(1000, $cellRowSpan)->addText('№ п/п', [], $cellHCentered);
        $table->addCell(3300, $cellRowSpan)->addText('Альтернативные решения', [], $cellHCentered);
        $table->addCell(7500, $cellColSpan)->addText('Критерии и ограничения принятия решения', [], $cellHCentered);
        $table->addCell(2200, $cellRowSpan)->addText('Бальная оценка (max. 100 баллов)', [], $cellHCentered);

        $table->addRow();
        $table->addCell(1000, $cellRowContinue);
        $table->addCell(3300, $cellRowContinue);
        if ($technicalMap->getCriterionTitle4()) {
            $table->addCell(1875, $cellVCentered)->addText('1', [], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText('2', [], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText('3', [], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText('4', [], $cellHCentered);
        } elseif ($technicalMap->getCriterionTitle3()) {
            $table->addCell(2500, $cellVCentered)->addText('1', [], $cellHCentered);
            $table->addCell(2500, $cellVCentered)->addText('2', [], $cellHCentered);
            $table->addCell(2500, $cellVCentered)->addText('3', [], $cellHCentered);
        } else {
            $table->addCell(3750, $cellVCentered)->addText('1', [], $cellHCentered);
            $table->addCell(3750, $cellVCentered)->addText('2', [], $cellHCentered);
        }
        $table->addCell(2200, $cellRowContinue);

        $table->addRow();
        $table->addCell(1000, $cellRowContinue);
        $table->addCell(3300, $cellRowContinue);
        if ($technicalMap->getCriterionTitle4()) {
            $table->addCell(1875, $cellVCentered)->addText($technicalMap->getCriterionTitle1(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText($technicalMap->getCriterionTitle2(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText($technicalMap->getCriterionTitle3(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellVCentered)->addText($technicalMap->getCriterionTitle4(), ['bold' => true], $cellHCentered);
        } elseif ($technicalMap->getCriterionTitle3()) {
            $table->addCell(2500, $cellVCentered)->addText($technicalMap->getCriterionTitle1(), ['bold' => true], $cellHCentered);
            $table->addCell(2500, $cellVCentered)->addText($technicalMap->getCriterionTitle2(), ['bold' => true], $cellHCentered);
            $table->addCell(2500, $cellVCentered)->addText($technicalMap->getCriterionTitle3(), ['bold' => true], $cellHCentered);
        } else {
            $table->addCell(3750, $cellVCentered)->addText($technicalMap->getCriterionTitle1(), ['bold' => true], $cellHCentered);
            $table->addCell(3750, $cellVCentered)->addText($technicalMap->getCriterionTitle2(), ['bold' => true], $cellHCentered);
        }
        $table->addCell(2200, $cellRowContinue);

        $table->addRow();
        $table->addCell(4300, ['gridSpan' => 2, 'valign' => 'center', 'bgcolor' => '#D7D7D7'])->addText('Max баллов', [], $cellHCentered);
        if ($technicalMap->getCriterionTitle4()) {
            $table->addCell(1875, $cellBgColor)->addText($technicalMap->getMaxPoints1(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellBgColor)->addText($technicalMap->getMaxPoints2(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellBgColor)->addText($technicalMap->getMaxPoints3(), ['bold' => true], $cellHCentered);
            $table->addCell(1875, $cellBgColor)->addText($technicalMap->getMaxPoints4(), ['bold' => true], $cellHCentered);
        } elseif ($technicalMap->getCriterionTitle3()) {
            $table->addCell(2500, $cellBgColor)->addText($technicalMap->getMaxPoints1(), ['bold' => true], $cellHCentered);
            $table->addCell(2500, $cellBgColor)->addText($technicalMap->getMaxPoints2(), ['bold' => true], $cellHCentered);
            $table->addCell(2500, $cellBgColor)->addText($technicalMap->getMaxPoints3(), ['bold' => true], $cellHCentered);
        } else {
            $table->addCell(3750, $cellBgColor)->addText($technicalMap->getMaxPoints1(), ['bold' => true], $cellHCentered);
            $table->addCell(3750, $cellBgColor)->addText($technicalMap->getMaxPoints2(), ['bold' => true], $cellHCentered);
        }
        $table->addCell(2200, $cellBgColor)->addText('100', ['bold' => true], $cellHCentered);
        $currentRow = 1;

        if ($technicalMap->getTechnicalMapSolutions()) {
            /** @var TechnicalMapSolutions $solution */
            foreach ($technicalMap->getTechnicalMapSolutions() as $solution) {
                if (!$solution->isDeleted()) {
                    $table->addRow();
                    $table->addCell(1000, $cellRowSpan)->addText($currentRow, [], $cellHCentered);
                    $table->addCell(3000, $cellRowSpan)->addText($solution->getName(), [], $cellHCentered);

                    if ($technicalMap->getCriterionTitle4()) {
                        $table->addCell(1875, $cellVCentered)->addText($solution->getCriterion1(), [], $cellHCentered);
                        $table->addCell(1875, $cellVCentered)->addText($solution->getCriterion2(), [], $cellHCentered);
                        $table->addCell(1875, $cellVCentered)->addText($solution->getCriterion3(), [], $cellHCentered);
                        $table->addCell(1875, $cellVCentered)->addText($solution->getCriterion4(), [], $cellHCentered);
                        $table->addCell(2200, $cellRowSpan)->addText($points = $solution->getPoints1() + $solution->getPoints2() + $solution->getPoints3() + $solution->getPoints4(), ['bold' => true], $cellHCentered);

                        $table->addRow();
                        $table->addCell(1000, $cellRowContinue);
                        $table->addCell(3000, $cellRowContinue);
                        $table->addCell(1875, $cellBgColor)->addText($solution->getPoints1(), ['bold' => true], $cellHCentered);
                        $table->addCell(1875, $cellBgColor)->addText($solution->getPoints2(), ['bold' => true], $cellHCentered);
                        $table->addCell(1875, $cellBgColor)->addText($solution->getPoints3(), ['bold' => true], $cellHCentered);
                        $table->addCell(1875, $cellBgColor)->addText($solution->getPoints4(), ['bold' => true], $cellHCentered);
                        $table->addCell(2200, $cellRowContinue);
                    } elseif ($technicalMap->getCriterionTitle3()) {
                        $table->addCell(2500, $cellVCentered)->addText($solution->getCriterion1(), [], $cellHCentered);
                        $table->addCell(2500, $cellVCentered)->addText($solution->getCriterion2(), [], $cellHCentered);
                        $table->addCell(2500, $cellVCentered)->addText($solution->getCriterion3(), [], $cellHCentered);
                        $table->addCell(2200, $cellRowSpan)->addText($points = $solution->getPoints1() + $solution->getPoints2() + $solution->getPoints3(), ['bold' => true], $cellHCentered);

                        $table->addRow();
                        $table->addCell(1000, $cellRowContinue);
                        $table->addCell(3000, $cellRowContinue);
                        $table->addCell(2500, $cellBgColor)->addText($solution->getPoints1(), ['bold' => true], $cellHCentered);
                        $table->addCell(2500, $cellBgColor)->addText($solution->getPoints2(), ['bold' => true], $cellHCentered);
                        $table->addCell(2500, $cellBgColor)->addText($solution->getPoints3(), ['bold' => true], $cellHCentered);
                        $table->addCell(2200, $cellRowContinue);
                    } else {
                        $table->addCell(3750, $cellVCentered)->addText($solution->getCriterion1(), [], $cellHCentered);
                        $table->addCell(3750, $cellVCentered)->addText($solution->getCriterion2(), [], $cellHCentered);
                        $table->addCell(2200, $cellRowSpan)->addText($points = $solution->getPoints1() + $solution->getPoints2(), ['bold' => true], $cellHCentered);

                        $table->addRow();
                        $table->addCell(1000, $cellRowContinue);
                        $table->addCell(3000, $cellRowContinue);
                        $table->addCell(3750, $cellBgColor)->addText($solution->getPoints1(), ['bold' => true], $cellHCentered);
                        $table->addCell(3750, $cellBgColor)->addText($solution->getPoints2(), ['bold' => true], $cellHCentered);
                        $table->addCell(2200, $cellRowContinue);
                    }
                    $currentRow++;
                }
            }
        }

        $signatories = [];

        foreach ($technicalMap->getSignatories() as $signatory) {
            $signatories[] = $signatory->getSignatory()->getLastNameWithInitials();
        }

        $section->addTextBreak();

        if ($recommendedSolution) {
            /** @var TechnicalMapSolutions $recommendedSolution */
            $text = 'Рекомендованное решение (технология): ' . $recommendedSolution->getName();
            Html::addHtml($section, $text);
            
            $text = 'Обоснование выбора: ' . ($recommendedSolution->getJustification() ? $recommendedSolution->getJustification() : '') ;
            Html::addHtml($section, $text);
        }

        $text = 'Составил:   ' . $technicalMap->getOwner()->getLastNameWithInitials() . ' (' . $technicalMap->getOwner()->getEmployeeRole() . ')';
        Html::addHtml($section, $text);

        $text = ('Подписанты: ' . ' ' . join(', ', $signatories));
        Html::addHtml($section, $text);

        $text = 'Статус КВУР/КВТР: ' . $this->translator->trans($technicalMap->getStatusList()[$technicalMap->getStatus()]) . '.';
        Html::addHtml($section, $text);

        return $phpWord;
    }
}