<?php

namespace DocumentBundle\Service\Export;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class DocumentBuilder
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
     * @param $lastRevision
     * @return PhpWord
     */
    public function build($document, $lastRevision)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $properties = $phpWord->getDocInfo();
        $properties->setCreator($document->getOwner());
        $properties->setCompany('Андроидная техника');
        $properties->setLastModifiedBy('Olymp');
        $properties->setTitle('Выгрузка');
        $properties->setDescription('Документ');
        $properties->setSubject('Документ');

        $section = $phpWord->addSection();

        $header = $section->addHeader();
        $imageStyle = [
            'width' => 500,
            'align' => 'center'
        ];
        $header->addImage('images/colontitles/headerAT.png', $imageStyle);

        $footer = $section->addFooter();
        $imageStyle = [
            'width' => 455,
            'align' => 'center'
        ];
        $footer->addImage('images/colontitles/footerAS.png', $imageStyle);

        $title = $this->translator->trans('Document') . ' № ' . $document->getCode();
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $section->addTextBreak();
        
        Html::addHtml($section, $lastRevision->getContent());

        return $phpWord;
    }
}