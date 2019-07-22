<?php

namespace InfrastructureBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ComputerDiff
 *
 * @ORM\Table(name="computer_diff")
 * @ORM\Entity(repositoryClass="InfrastructureBundle\Repository\ComputerDiffRepository")
 */
class ComputerDiff
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="changed_by_id", referencedColumnName="id")
     */
    private $changedBy;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\Computer")
     * @ORM\JoinColumn(name="computer_id", referencedColumnName="id", nullable=true)
     */
    private $computer;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="computer_part_id", referencedColumnName="id", nullable=true)
     */
    private $computerPart;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=255)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="text", nullable=true)
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="text", nullable=true)
     */
    private $newValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
     * Set changedBy
     *
     * @param User $changedBy
     *
     * @return ComputerDiff
     */
    public function setChangedBy($changedBy)
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    /**
     * Get changedBy
     *
     * @return User
     */
    public function getChangedBy()
    {
        return $this->changedBy;
    }

    /**
     * Set computer
     *
     * @param Computer $computer
     *
     * @return ComputerDiff
     */
    public function setComputer($computer)
    {
        $this->computer = $computer;

        return $this;
    }

    /**
     * Get computer
     *
     * @return Computer
     */
    public function getComputer()
    {
        return $this->computer;
    }

    /**
     * Set computerPart
     *
     * @param ComputerPart $computerPart
     *
     * @return ComputerDiff
     */
    public function setComputerPart($computerPart)
    {
        $this->computerPart = $computerPart;

        return $this;
    }

    /**
     * Get computerPart
     *
     * @return ComputerPart
     */
    public function getComputerPart()
    {
        return $this->computerPart;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return ComputerDiff
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     *
     * @return ComputerDiff
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ComputerDiff
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return ComputerDiff
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}