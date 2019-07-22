<?php

namespace ProductionBundle\Entity;

use AppBundle\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ware
 *
 * @ORM\Table(name="ware")
 * @ORM\Entity(repositoryClass="ProductionBundle\Repository\WareRepository")
 */
class Ware
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @ORM\OneToMany(targetEntity="PurchaseBundle\Entity\PurchaseRequest", mappedBy="ware", cascade="all")
     */
    private $purchaseRequests;

    public function __construct()
    {
        $this->purchaseRequests = new ArrayCollection();
    }


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
     * Set name
     *
     * @param string $name
     *
     * @return Ware
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Ware
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return ArrayCollection|Ware[]
     */
    public function getPurchaseRequests()
    {
        return $this->purchaseRequests;
    }

    /**
     * @param mixed $purchaseRequests
     */
    public function setPurchaseRequests($purchaseRequests)
    {
        $this->purchaseRequests = $purchaseRequests;
    }
}