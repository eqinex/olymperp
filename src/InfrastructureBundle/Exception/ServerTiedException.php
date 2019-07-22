<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 27.02.19
 * Time: 10:39
 */

namespace InfrastructureBundle\Exception;

use Exception;

class ServerTiedException extends Exception
{
    /** @var string $message */
    public $message = 'Unable to delete! Server %s is tied by %s';

    /**
     * ComputerPartTiedException constructor.
     * @param $translator
     * @param $serverName
     * @param $serverTiedName
     */
    public function __construct($translator, $serverName, $serverTiedName)
    {
        parent::__construct(sprintf($translator->trans($this->message), $serverName, $serverTiedName));
    }
}