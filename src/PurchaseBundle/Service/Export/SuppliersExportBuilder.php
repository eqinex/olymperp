<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 30.04.19
 * Time: 14:38
 */

namespace PurchaseBundle\Service\Export;

use DocumentBundle\Entity\Document;
use PurchaseBundle\Entity\Supplier;

class SuppliersExportBuilder
{
    /**
     * @param $suppliers
     * @return string
     */
    public function build($suppliers)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <ФайлОбмена>
        <ПравилаОбмена>
            <Источник>КомплекснаяАвтоматизация</Источник>
            <Приемник>КомплекснаяАвтоматизация</Приемник>
            <Параметры/>
            <Обработки/>
            <ПравилаКонвертацииОбъектов>
                <Правило>
                    <Код>ДоговорыКонтрагентов</Код>
                    <СинхронизироватьПоИдентификатору>true</СинхронизироватьПоИдентификатору>
                    <ГенерироватьНовыйНомерИлиКодЕслиНеУказан>true</ГенерироватьНовыйНомерИлиКодЕслиНеУказан>
                    <Источник>СправочникСсылка.ДоговорыКонтрагентов</Источник>
                    <Приемник>СправочникСсылка.ДоговорыКонтрагентов</Приемник>
                </Правило>
                <Правило>
                    <Код>Контрагенты</Код>
                    <СинхронизироватьПоИдентификатору>true</СинхронизироватьПоИдентификатору>
                    <ГенерироватьНовыйНомерИлиКодЕслиНеУказан>true</ГенерироватьНовыйНомерИлиКодЕслиНеУказан>
                    <Источник>СправочникСсылка.Контрагенты</Источник>
                    <Приемник>СправочникСсылка.Контрагенты</Приемник>
                </Правило>
            </ПравилаКонвертацииОбъектов>
            <ПравилаОчисткиДанных/>
            <Алгоритмы/>
            <Запросы/>
        </ПравилаОбмена>';

        $count = 1;
        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $xml .= '<Объект Нпп="' . $count . '" Тип="СправочникСсылка.Контрагенты" ИмяПравила="Контрагенты">';
            $xml .= '<Ссылка Нпп="' . $count . '">';

            $xml .= '<Свойство Имя="{УникальныйИдентификатор}" Тип="Строка">';
            $xml = $this->addValue($xml, $supplier->getOneSUniqueCode());
            $xml .= '</Свойство>';

            $xml .= '</Ссылка>';

            $xml .= '<Свойство Имя="НаименованиеПолное" Тип="Строка">';
            if ($supplier->getFullTitle()) {
                $xml = $this->addValue($xml, $supplier->getFullTitle());
            } else {
                $xml .= '<Пусто/>';
            }
            $xml .= '</Свойство>';

            $xml .= '<Свойство Имя="Наименование" Тип="Строка">';
            $xml = $this->addValue($xml, $supplier->getTitle());
            $xml .= '</Свойство>';

            $xml .= '<Свойство Имя="ИНН" Тип="Строка">';
            $xml = $this->addValue($xml, $supplier->getItn());
            $xml .= '</Свойство>';

            $xml .= '<Свойство Имя="КПП" Тип="Строка">';
            if ($supplier->getKpp()) {
                $xml = $this->addValue($xml, $supplier->getKpp());
            } else {
                $xml .= '<Пусто/>';
            }
            $xml .= '</Свойство>';

            $xml .= '</Объект>';

            $objectCount = $count;
            $count++;

            if ($supplier->getDocuments()) {
                /** @var Document $document */
                foreach ($supplier->getDocuments() as $document) {
                    $xml .= '<Объект Нпп="' . $count . '" Тип="СправочникСсылка.ДоговорыКонтрагентов" ИмяПравила="ДоговорыКонтрагентов">';
                    $xml .= '<Ссылка Нпп="' . $count . '">';

                    $xml .= '<Свойство Имя="{УникальныйИдентификатор}" Тип="Строка">';
                    $xml = $this->addValue($xml, $document->getOneSUniqueCode());
                    $xml .= '</Свойство>';

                    $xml .= '</Ссылка>';

                    $xml .= '<Свойство Имя="Дата" Тип="Дата">';
                    if ($document->getStartAt()) {
                        $xml = $this->addValue($xml, $document->getStartAt()->format('Y-m-d') . 'T' . $document->getStartAt()->format('H:i:s'));
                    } else {
                        $xml .= '<Пусто/>';
                    }
                    $xml .= '</Свойство>';

                    $xml .= '<Свойство Имя="Владелец" Тип="СправочникСсылка.Контрагенты">';
                    $xml .= '<Ссылка Нпп="' . $objectCount . '">';

                    $xml .= '<Свойство Имя="{УникальныйИдентификатор}" Тип="Строка">';
                    $xml = $this->addValue($xml, $supplier->getOneSUniqueCode());
                    $xml .= '</Свойство>';

                    $xml .= '</Ссылка>';
                    $xml .= '</Свойство>';

                    $xml .= '<Свойство Имя="Наименование" Тип="Строка">';
                    $xml = $this->addValue($xml, $document->getCode());
                    $xml .= '</Свойство>';

                    $xml .= '<Свойство Имя="Номер" Тип="Строка">';
                    $xml = $this->addValue($xml, $document->getCode());
                    $xml .= '</Свойство>';

                    $xml .= '<Свойство Имя="СрокДействия" Тип="Дата">';
                    if ($document->getEndAt()) {
                        $xml = $this->addValue($xml, $document->getEndAt()->format('Y-m-d') . 'T' . $document->getEndAt()->format('H:i:s'));
                    } else {
                        $xml .= '<Пусто/>';
                    }
                    $xml .= '</Свойство>';

                    $xml .= '</Объект>';

                    $count ++;
                }
            }
        }

        $xml .= '</ФайлОбмена>';

        return $xml;
    }

    /**
     * @param $xml
     * @param $value
     * @return string
     */
    protected function addValue($xml, $value)
    {
        $xml .= '<Значение>';
        $xml .= $value;
        $xml .= '</Значение>';

        return $xml;
    }
}