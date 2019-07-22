<?php


namespace AppBundle\Service;

use AppBundle\Entity\MonitoringHostname;
use AppBundle\Repository\RepositoryAwareTrait;

class MonitoringService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param MonitoringHostname $hostname
     * @return mixed
     */
    public function getLastFiveDataMonitoring($hostname)
    {
        return $this->getMonitoringRepository()->getLastFiveDataMonitoring($hostname);
    }
}