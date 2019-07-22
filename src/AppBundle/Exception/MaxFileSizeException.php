<?php

namespace AppBundle\Exception;

use Exception;

class MaxFileSizeException extends Exception
{
    /**
     * MaxFileSizeException constructor.
     * @param $translator
     * @param $argc
     */
    public function __construct($translator, $argc)
    {
        parent::__construct(sprintf($translator->trans('File %s upload error! Maximum file size 100 MB'), $argc));
    }
}