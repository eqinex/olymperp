<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use ProductionBundle\Entity\Ware;

/**
 * Ware
 *
 * @ORM\Table(name="specification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpecificationRepository")
 */
class Specification
{
    const TYPE_FUNCTIONAL = 'functional_characteristic';
    const TYPE_TECHNICAL = 'technical_characteristic';

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
     * @ORM\ManyToOne(targetEntity="ProductionBundle\Entity\Ware")
     * @ORM\JoinColumn(name="ware", referencedColumnName="id")
     */
    private $ware;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="value_task", type="string", length=255)
     */
    private $valueTask;

    /**
     * @var string
     *
     * @ORM\Column(name="value_inner_task", type="string", length=255)
     */
    private $valueInnerTask;

    /**
     * @var string
     *
     * @ORM\Column(name="difference", type="string", length=255)
     */
    private $difference;

    /**
     * @var string
     *
     * @ORM\Column(name="notice", type="text", nullable=true)
     */
    private $notice;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

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
     * @return Specification
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
     * Set ware
     *
     * @param Ware $ware
     *
     * @return $this
     */
    public function setWare($ware)
    {
        $this->ware = $ware;

        return $this;
    }

    /**
     * Get ware
     *
     * @return Ware
     */
    public function getWare()
    {
        return $this->ware;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Specification
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return Specification
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Set valueTask
     *
     * @param string $valueTask
     *
     * @return Specification
     */
    public function setValueTask($valueTask)
    {
        $this->valueTask = $valueTask;

        return $this;
    }

    /**
     * Get valueTask
     *
     * @return string
     */
    public function getValueTask()
    {
        return $this->valueTask;
    }

    /**
     * Set valueInnerTask
     *
     * @param string $valueInnerTask
     *
     * @return Specification
     */
    public function setValueInnerTask($valueInnerTask)
    {
        $this->valueInnerTask = $valueInnerTask;

        return $this;
    }

    /**
     * Get valueInnerTask
     *
     * @return string
     */
    public function getValueInnerTask()
    {
        return $this->valueInnerTask;
    }

    /**
     * Set difference
     *
     * @param string $difference
     *
     * @return Specification
     */
    public function setDifference($difference)
    {
        $this->difference = $difference;

        return $this;
    }

    /**
     * Get difference
     *
     * @return string
     */
    public function getDifference()
    {
        return $this->difference;
    }

    /**
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param string $notice
     * @return Specification
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
        return $this;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_FUNCTIONAL => self::TYPE_FUNCTIONAL,
            self::TYPE_TECHNICAL => self::TYPE_TECHNICAL,
        ];
    }

}