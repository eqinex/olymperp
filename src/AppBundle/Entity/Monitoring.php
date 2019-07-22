<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Monitoring
 *
 * @ORM\Table(name="monitoring")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonitoringRepository")
 */
class Monitoring
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
     * @ORM\Column(name="disk", type="string", length=255)
     */
    private $disk;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @var int
     *
     * @ORM\Column(name="free", type="integer")
     */
    private $free;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MonitoringHostname")
     * @ORM\JoinColumn(name="hostname_id", referencedColumnName="id")
     */
    private $hostname;

    /**
     * @var int
     *
     * @ORM\Column(name="memtotal", type="integer")
     */
    private $memtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="memavail", type="integer")
     */
    private $memavail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="uptime", type="integer")
     */
    private $uptime;

    /**
     * @var float
     *
     * @ORM\Column(name="load_average_minute", type="float")
     */
    private $loadAverageMinute;

    /**
     * @var float
     *
     * @ORM\Column(name="load_average_five_minutes", type="float")
     */
    private $loadAverageFiveMinutes;

    /**
     * @var float
     *
     * @ORM\Column(name="load_average_fifteen_minutes", type="float")
     */
    private $loadAverageFifteenMinutes;

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
     * @param string $disk
     * @return Monitoring
     */
    public function setDisk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @param integer $total
     * @return Monitoring
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param integer $free
     * @return Monitoring
     */
    public function setFree($free)
    {
        $this->free = $free;

        return $this;
    }

    /**
     * @return integer
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param $hostname
     * @return $this
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * @param integer $memtotal
     * @return Monitoring
     */
    public function setMemtotal($memtotal)
    {
        $this->memtotal = $memtotal;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMemtotal()
    {
        return $this->memtotal;
    }

    /**
     * @param integer $memavail
     * @return Monitoring
     */
    public function setMemavail($memavail)
    {
        $this->memavail = $memavail;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMemavail()
    {
        return $this->memavail;
    }

    /**
     * @param \DateTime $createdAt
     * @return Monitoring
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param integer $uptime
     * @return Monitoring
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;

        return $this;
    }

    /**
     * @return integer
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param float $loadAverageMinute
     * @return Monitoring
     */
    public function setLoadAverageMinute($loadAverageMinute)
    {
        $this->loadAverageMinute = $loadAverageMinute;

        return $this;
    }

    /**
     * @return float
     */
    public function getLoadAverageMinute()
    {
        return $this->loadAverageMinute;
    }

    /**
     * @param float $loadAverageFiveMinutes
     * @return Monitoring
     */
    public function setLoadAverageFiveMinutes($loadAverageFiveMinutes)
    {
        $this->loadAverageFiveMinutes = $loadAverageFiveMinutes;

        return $this;
    }

    /**
     * @return float
     */
    public function getLoadAverageFiveMinutes()
    {
        return $this->loadAverageFiveMinutes;
    }

    /**
     * @param float $loadAverageFifteenMinutes
     * @return Monitoring
     */
    public function setLoadAverageFifteenMinutes($loadAverageFifteenMinutes)
    {
        $this->loadAverageFifteenMinutes = $loadAverageFifteenMinutes;

        return $this;
    }

    /**
     * @return float
     */
    public function getLoadAverageFifteenMinutes()
    {
        return $this->loadAverageFifteenMinutes;
    }
}
