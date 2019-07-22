<?php

namespace InfrastructureBundle\Exception;

use Exception;

class ComputerPartTiedException extends Exception
{
    /** @var string $message */
    public $message = 'Unable to delete! %s (%s) is tied to %s!';

    /**
     * ComputerPartTiedException constructor.
     * @param $translator
     * @param $partType
     * @param $partName
     * @param $computerName
     */
    public function __construct($translator, $partName, $partType, $computerName)
    {
        parent::__construct(sprintf($translator->trans($this->message), $partName, $translator->trans($partType), $computerName));
    }
}