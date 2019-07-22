<?php

namespace DevelopmentBundle\Exception;

use Exception;

class NonUniqueEngineeringDocumentException extends Exception
{
    /** @var string $message */
    public $message = 'Error adding entry! Entry %s already exists!';

    /**
     * NonUniqueEngineeringDocumentException constructor.
     * @param $translator
     * @param $argc
     */
    public function __construct($translator, $argc)
    {
        parent::__construct(sprintf($translator->trans($this->message), $argc));
    }
}