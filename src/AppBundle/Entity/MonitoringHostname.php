<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MonitoringHostname
 *
 * @ORM\Table(name="monitoring_hostname")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonitoringHostnameRepository")
 */
class MonitoringHostname
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255)
     */
    private $hostname;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Monitoring", mappedBy="hostname", cascade="all")
     */
    private $data;

    public function __construct()
    {
        $this->data = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $hostname
     * @return MonitoringHostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return ArrayCollection|Monitoring[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
