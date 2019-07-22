<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestMovement
 *
 * @ORM\Table(name="request_movement")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\RequestMovementRepository")
 */
class RequestMovement
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text")
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="text")
     */
    private $destination;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="sendResponsible", type="text")
     */
    private $sendResponsible;

    /**
     * @var string
     *
     * @ORM\Column(name="receiveResponsible", type="text")
     */
    private $receiveResponsible;

    /**
     * @var bool
     *
     * @ORM\Column(name="needPrr", type="boolean", nullable=true)
     */
    private $needPrr;

    /**
     * @var bool
     *
     * @ORM\Column(name="needCargoDescription", type="boolean", nullable=true)
     */
    private $needCargoDescription;

    /**
     * @var bool
     *
     * @ORM\Column(name="needCargoInsurance", type="boolean", nullable=true)
     */
    private $needCargoInsurance;

    /**
     * @var bool
     *
     * @ORM\Column(name="needAdditionalCargo", type="boolean", nullable=true)
     */
    private $needAdditionalCargo;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $request;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return RequestMovement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return RequestMovement
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set destination
     *
     * @param string $destination
     *
     * @return RequestMovement
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return RequestMovement
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return RequestMovement
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set sendResponsible
     *
     * @param string $sendResponsible
     *
     * @return RequestMovement
     */
    public function setSendResponsible($sendResponsible)
    {
        $this->sendResponsible = $sendResponsible;

        return $this;
    }

    /**
     * Get sendResponsible
     *
     * @return string
     */
    public function getSendResponsible()
    {
        return $this->sendResponsible;
    }

    /**
     * Set receiveResponsible
     *
     * @param string $receiveResponsible
     *
     * @return RequestMovement
     */
    public function setReceiveResponsible($receiveResponsible)
    {
        $this->receiveResponsible = $receiveResponsible;

        return $this;
    }

    /**
     * Get receiveResponsible
     *
     * @return string
     */
    public function getReceiveResponsible()
    {
        return $this->receiveResponsible;
    }

    /**
     * Set needPrr
     *
     * @param boolean $needPrr
     *
     * @return RequestMovement
     */
    public function setNeedPrr($needPrr)
    {
        $this->needPrr = $needPrr;

        return $this;
    }

    /**
     * Get needPrr
     *
     * @return bool
     */
    public function getNeedPrr()
    {
        return $this->needPrr;
    }

    /**
     * Set needCargoDescription
     *
     * @param boolean $needCargoDescription
     *
     * @return RequestMovement
     */
    public function setNeedCargoDescription($needCargoDescription)
    {
        $this->needCargoDescription = $needCargoDescription;

        return $this;
    }

    /**
     * Get needCargoDescription
     *
     * @return bool
     */
    public function getNeedCargoDescription()
    {
        return $this->needCargoDescription;
    }

    /**
     * Set needCargoInsurance
     *
     * @param boolean $needCargoInsurance
     *
     * @return RequestMovement
     */
    public function setNeedCargoInsurance($needCargoInsurance)
    {
        $this->needCargoInsurance = $needCargoInsurance;

        return $this;
    }

    /**
     * Get needCargoInsurance
     *
     * @return bool
     */
    public function getNeedCargoInsurance()
    {
        return $this->needCargoInsurance;
    }

    /**
     * Set needAdditionalCargo
     *
     * @param boolean $needAdditionalCargo
     *
     * @return RequestMovement
     */
    public function setNeedAdditionalCargo($needAdditionalCargo)
    {
        $this->needAdditionalCargo = $needAdditionalCargo;

        return $this;
    }

    /**
     * Get needAdditionalCargo
     *
     * @return bool
     */
    public function getNeedAdditionalCargo()
    {
        return $this->needAdditionalCargo;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return RequestMovement
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }
}

