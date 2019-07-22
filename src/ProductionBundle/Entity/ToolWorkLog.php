<?php

namespace ProductionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ToolWorkLog
 *
 * @ORM\Table(name="tool_work_log")
 * @ORM\Entity(repositoryClass="ProductionBundle\Repository\ToolWorkLogRepository")
 */

class ToolWorkLog
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
     * @ORM\Column(name="designation", type="string", length=255)
     */
    private $designation;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="ProductionBundle\Entity\Tool")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id")
     */

    private $tool;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="consumption_of_basic_materials", type="float")
     */
    private $consumptionOfBasicMaterials;

    /**
     * @var float
     *
     * @ORM\Column(name="support_materials_consumption", type="float")
     */
    private $supportMaterialsConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="placement", type="float")
     */
    private $placement;

    /**
     * @var float
     *
     * @ORM\Column(name="printing_time", type="float")
     */
    private $printingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * ToolWorkLog constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set tool
     *
     * @param Tool $tool
     *
     * @return $this
     */
    public function setTool($tool)
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * Get tool
     *
     * @return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return ToolWorkLog
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return ToolWorkLog
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ToolWorkLog
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return ToolWorkLog
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set consumptionOfBasicMaterials
     *
     * @param float $consumptionOfBasicMaterials
     *
     * @return ToolWorkLog
     */
    public function setConsumptionOfBasicMaterials($consumptionOfBasicMaterials)
    {
        $this->consumptionOfBasicMaterials = $consumptionOfBasicMaterials;

        return $this;
    }

    /**
     * Get consumptionOfBasicMaterials
     *
     * @return float
     */
    public function getConsumptionOfBasicMaterials()
    {
        return $this->consumptionOfBasicMaterials;
    }

    /**
     * Set supportMaterialsConsumption
     *
     * @param float $supportMaterialsConsumption
     *
     * @return ToolWorkLog
     */
    public function setSupportMaterialsConsumption($supportMaterialsConsumption)
    {
        $this->supportMaterialsConsumption = $supportMaterialsConsumption;

        return $this;
    }

    /**
     * Get supportMaterialsConsumption
     *
     * @return float
     */
    public function getSupportMaterialsConsumption()
    {
        return $this->supportMaterialsConsumption;
    }

    /**
     * Set placement
     *
     * @param float $placement
     *
     * @return ToolWorkLog
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return float
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set printingTime
     *
     * @param float $printingTime
     *
     * @return ToolWorkLog
     */
    public function setPrintingTime($printingTime)
    {
        $this->printingTime = $printingTime;

        return $this;
    }

    /**
     * Get printingTime
     *
     * @return float
     */
    public function getPrintingTime()
    {
        return $this->printingTime;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ToolWorkLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}