<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TechnicalMapSignatory
 *
 * @ORM\Table(name="technical_map_signatory")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\TechnicalMapSignatoryRepository")
 */
class TechnicalMapSignatory
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
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\TechnicalMap")
     * @ORM\JoinColumn(name="technical_map_id", referencedColumnName="id")
     */
    private $technicalMap;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="signatory_id", referencedColumnName="id")
     */
    private $signatory;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved = false;

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
     * @param \stdClass $technicalMap
     *
     * @return TechnicalMapSignatory
     */
    public function setTechnicalMap($technicalMap)
    {
        $this->technicalMap = $technicalMap;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getTechnicalMap()
    {
        return $this->technicalMap;
    }

    /**
     * @param \stdClass $signatory
     *
     * @return TechnicalMapSignatory
     */
    public function setSignatory($signatory)
    {
        $this->signatory = $signatory;

        return $this;
    }

    /**
     * Get signatory
     *
     * @return \stdClass
     */
    public function getSignatory()
    {
        return $this->signatory;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     * @return TechnicalMapSignatory
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }
}