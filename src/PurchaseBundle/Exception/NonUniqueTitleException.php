<?php

namespace PurchaseBundle\Exception;

use Exception;

class NonUniqueTitleException extends Exception
{
    /** @var string $message */
    public $message = 'Supplier with name %s already exists!';

    /**
     * NonUniqueTitleException constructor.
     * @param $translator
     * @param $argc
     */
    public function __construct($translator, $argc)
    {
        parent::__construct(sprintf($translator->trans($this->message), $argc));
    }
}