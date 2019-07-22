<?php

namespace DocumentBundle\Service\Export;

use AppBundle\Entity\User;
use DocumentBundle\Entity\Document;
use DocumentBundle\Entity\DocumentSignatory;
use PhpOffice\PhpWord\PhpWord;

class DocumentCardBuilder
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
     * @param $supplementaryAgreements
     * @return PhpWord
     */
    public function build($document, $supplementaryAgreements)
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

        $title = 'Карточка договора/Лист согласования';
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];
        $cellColSpan = ['gridSpan' => 3, 'valign' => 'center'];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Проект', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getProject()->getName(), ['bold' => true, 'italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        /** @var User $leader */
        $leader = $document->getProject()->getLeader();
        $table->addCell(2000, $cellVCentered)->addText('Руководитель проекта', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(4000, $cellVCentered)->addText($leader->getFullname(true), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(4000, $cellVCentered)->addText('Подразделение руководителя', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText($leader->getTeam() ? $leader->getTeam()->getTitle() : '-', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Куратор договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getCurator()->getFullname(true), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Подразделение куратора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getCurator()->getTeam() ? $document->getCurator()->getTeam()->getTitle() : '-', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Инициатор договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getOwner()->getFullname(true), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Подразделение инициатора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getOwner()->getTeam() ? $document->getOwner()->getTeam()->getTitle() : '-', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Категория', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getCategory()->getName(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Вид договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getDocumentTemplate()->getTitle(), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Бланк договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($this->translator->trans($document->getType()), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Статус', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($this->translator->trans($document->getStatusList()[$document->getStatus()]), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Срок исполнения в мес.', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getPeriod(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Номер договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getCode(), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Номер договора контрагента', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getSupplierContractCode(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Дата договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getCreatedAt()->format('d.m.Y. H:i'), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Документ обновлен', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getUpdatedAt()->format('d.m.Y H:i'), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Сумма по договору, руб.', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(number_format($document->getAmount(), 2, ',', ' '), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('НДС, руб.', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getVat() && $document->getVat() != 0 ? number_format($document->getVat(), 2, ',', ' ') : 'Без НДС', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Договор с', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getStartAt()->format('d.m.Y'), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Бессрочный', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->isUnLimited() == 0 ? 'Нет' : 'Да', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Договор по', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getEndAt()->format('d.m.Y'), ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Продление договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->isContractExtension() == 0 ? 'Не предусмотрено' : 'Предусмотрено', ['italic' => true, 'size' => 11], $cellHCentered);

        if ($document->getParentDocument()) {
            $table->addRow();
            $table->addCell(2000, $cellVCentered)->addText('Основное соглашение', ['bold' => true, 'size' => 11], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText($document->getParentDocument()->getCode(), ['italic' => true, 'size' => 11], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText('Дата основного соглашения', ['bold' => true, 'size' => 11], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText($document->getParentDocument()->getStartAt()->format('d.m.Y'), ['italic' => true, 'size' => 11], $cellHCentered);
        }
        $currentAgreement = 1;
        if ($supplementaryAgreements) {
            /** @var Document $agreement */
            foreach ($supplementaryAgreements as $agreement) {
                $table->addRow();
                $table->addCell(2000, $cellVCentered)->addText('Дополнительное соглашение №' . $currentAgreement, ['bold' => true, 'size' => 11], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText($agreement->getCode(), ['italic' => true, 'size' => 11], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText('Дата доп. соглашения', ['bold' => true, 'size' => 11], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText($agreement->getStartAt()->format('d.m.Y'), ['italic' => true, 'size' => 11], $cellHCentered);
                $currentAgreement++;
            }
        }

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Предмет договора', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getSubject(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Мера ответственности', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getMeasureOfResponsibility(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Обеспечение', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getSecurity(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Контрагент', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getSupplier() ? $document->getSupplier()->getTitle() : '-', ['italic' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('ИНН', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getSupplier()->getItn() ? $document->getSupplier()->getItn() : '-', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(6000, $cellColSpan)->addText('Дата признания дебиторской задолженности сомнительной/безнадежной', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText($document->getDebtReceivable() ? $document->getDebtReceivable()->format('d.m.Y') : '-', ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Акты сверки', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getAct(), ['italic' => true, 'size' => 11], $cellHCentered);

        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Комментарий', ['bold' => true, 'size' => 11], $cellHCentered);
        $table->addCell(6000, $cellColSpan)->addText($document->getComment(), ['italic' => true, 'size' => 11], $cellHCentered);

        if (!empty($document->getSignatoryUsers())) {
            $section->addTextBreak();

            $table = $section->addTable('Colspan Rowspan');

            $table->addRow();
            $table->addCell(500, $cellVCentered)->addText('№ п/п', ['bold' => true, 'size' => 11], $cellHCentered);
            $table->addCell(3000, $cellVCentered)->addText('Согласующий отдел', ['bold' => true, 'size' => 11], $cellHCentered);
            $table->addCell(5000, $cellVCentered)->addText('Согласовано / Не согласовано', ['bold' => true, 'size' => 11], $cellHCentered);
//            $table->addCell(3000, $cellVCentered)->addText('Замечания (причина не согласования)', ['bold' => true, 'size' => 11], $cellHCentered);
            $table->addCell(1500, $cellVCentered)->addText('Дата', ['bold' => true, 'size' => 11], $cellHCentered);

            $count = 1;
            /** @var DocumentSignatory $signatory */
            foreach ($document->getSignatories() as $signatory) {

                $employeeRole = $signatory->getSignatory()->getEmployeeRole() ?
                    $signatory->getSignatory()->getEmployeeRole()->getName() : '';

                $table->addRow();
                $table->addCell(500, $cellVCentered)->addText($count, ['size' => 11], $cellHCentered);
                $team = $signatory->getSignatory()->getTeam() ? $signatory->getSignatory()->getTeam()->getTitle() : '-';
                $table->addCell(3000, $cellVCentered)->addText($team
                    . '<w:br/>(' . $employeeRole
                    . ')<w:br/>' . $signatory->getSignatory()->getLastNameWithInitials(), ['size' => 11], $cellHCentered);
                $table->addCell(5000, $cellVCentered)->addText($signatory->isApproved() ? 'согласовано в СЭД' : 'не согласовано в СЭД', ['size' => 11], $cellHCentered);
//                $table->addCell(1000, $cellVCentered);
                $table->addCell(1500, $cellVCentered)->addText($signatory->getApprovedAt() ? $signatory->getApprovedAt()->format('d.m.Y') : '-', ['size' => 11], $cellHCentered);
                $count++;
            };
        }

        return $phpWord;
    }
}