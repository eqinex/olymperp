<?php

namespace AppBundle\Utils;


class DateUtils
{
    public static function transliterate($date)
    {
        $cyr = [
            'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля',
            'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'
        ];
        $lat = [
           'January', 'February', 'March', 'April', 'May', 'June', 'July',
            'August', 'September', 'October', 'November', 'December'
        ];

        return str_replace($lat, $cyr, $date);
    }
}