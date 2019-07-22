<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TechnicalMapSolutions
 *
 * @ORM\Table(name="technical_map_solutions")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\TechnicalMapSolutionsRepository")
 */
class TechnicalMapSolutions
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
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\TechnicalMap")
     * @ORM\JoinColumn(name="technical_map_id", referencedColumnName="id")
     */
    private $technicalMap;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_1", type="string", length=255, nullable=true)
     */
    private $criterion1;

    /**
     * @var int
     *
     * @ORM\Column(name="points_1", type="integer", nullable=true)
     */
    private $points1;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_2", type="string", length=255, nullable=true)
     */
    private $criterion2;

    /**
     * @var int
     *
     * @ORM\Column(name="points_2", type="integer", nullable=true)
     */
    private $points2;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_3", type="string", length=255, nullable=true)
     */
    private $criterion3;

    /**
     * @var int
     *
     * @ORM\Column(name="points_3", type="integer", nullable=true)
     */
    private $points3;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_4", type="string", length=255, nullable=true)
     */
    private $criterion4;

    /**
     * @var int
     *
     * @ORM\Column(name="points_4", type="integer", nullable=true)
     */
    private $points4;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @var bool
     *
     * @ORM\Column(name="selected", type="boolean", nullable=true)
     */
    private $selected;

    /**
     * @var string
     *
     * @ORM\Column(name="justification", type="text", nullable=true)
     */
    private $justification;

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
     * @return TechnicalMapSolutions
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
     * @return mixed
     */
    public function getTechnicalMap()
    {
        return $this->technicalMap;
    }

    /**
     * @param mixed $technicalMap
     */
    public function setTechnicalMap($technicalMap)
    {
        $this->technicalMap = $technicalMap;
    }

    /**
     * @param string $criterion1
     *
     * @return TechnicalMapSolutions
     */
    public function setCriterion1($criterion1)
    {
        $this->criterion1 = $criterion1;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterion1()
    {
        return $this->criterion1;
    }

    /**
     * @param string $points1
     *
     * @return TechnicalMapSolutions
     */
    public function setPoints1($points1)
    {
        $this->points1 = $points1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPoints1()
    {
        return $this->points1;
    }

    /**
     * @param string $criterion2
     *
     * @return TechnicalMapSolutions
     */
    public function setCriterion2($criterion2)
    {
        $this->criterion2 = $criterion2;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterion2()
    {
        return $this->criterion2;
    }

    /**
     * @param string $points2
     *
     * @return TechnicalMapSolutions
     */
    public function setPoints2($points2)
    {
        $this->points2 = $points2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPoints2()
    {
        return $this->points2;
    }

    /**
     * @param string $criterion3
     *
     * @return TechnicalMapSolutions
     */
    public function setCriterion3($criterion3)
    {
        $this->criterion3 = $criterion3;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterion3()
    {
        return $this->criterion3;
    }

    /**
     * @param string $points3
     *
     * @return TechnicalMapSolutions
     */
    public function setPoints3($points3)
    {
        $this->points3 = $points3;

        return $this;
    }

    /**
     * @return string
     */
    public function getPoints3()
    {
        return $this->points3;
    }

    /**
     * @param string $criterion4
     *
     * @return TechnicalMapSolutions
     */
    public function setCriterion4($criterion4)
    {
        $this->criterion4 = $criterion4;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterion4()
    {
        return $this->criterion4;
    }

    /**
     * @param string $points4
     *
     * @return TechnicalMapSolutions
     */
    public function setPoints4($points4)
    {
        $this->points4 = $points4;

        return $this;
    }

    /**
     * @return string
     */
    public function getPoints4()
    {
        return $this->points4;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     * @return TechnicalMapSolutions
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @param boolean $selected
     * @return TechnicalMapSolutions
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * @param string $justification
     * @return TechnicalMapSolutions
     */
    public function setJustification($justification)
    {
        $this->justification = $justification;

        return $this;
    }

    /**
     * @return string
     */
    public function getJustification()
    {
        return $this->justification;
    }
}