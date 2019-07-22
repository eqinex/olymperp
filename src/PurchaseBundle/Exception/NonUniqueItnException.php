<?php

namespace PurchaseBundle\Exception;

use Exception;

class NonUniqueItnException extends Exception
{
    /** @var string $message */
    public $message = 'Supplier with %s ITN already exists!';

    /**
     * NonUniqueItnException constructor.
     * @param $translator
     * @param $argc
     */
    public function __construct($translator, $argc)
    {
        parent::__construct(sprintf($translator->trans($this->message), $argc));
    }
}