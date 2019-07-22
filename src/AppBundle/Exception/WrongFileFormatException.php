<?php

namespace AppBundle\Exception;

use Exception;

class WrongFileFormatException extends Exception
{
    /**
     * WrongFileFormatException constructor.
     * @param $translator
     * @param $argc
     * @param $allowedFormats
     */
    public function __construct($translator, $argc, $allowedFormats)
    {
        parent::__construct(sprintf($translator->trans('File %s upload error! Only %s files can upload'), $argc, $allowedFormats));
    }
}