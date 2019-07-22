<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * ProjectDiff
 *
 * @ORM\Table(name="project_diff")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectDiffRepository")
 */
class ProjectDiff
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

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
     * @return ProjectDiff
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
     * Set task
     *
     * @param Project $project
     *
     * @return ProjectDiff
     */
    public function setTask($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get task
     *
     * @return Project
     */
    public function getTask()
    {
        return $this->project;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return ProjectDiff
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
     * @return ProjectDiff
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
     * @return ProjectDiff
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
     * @return detailsstring
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return ProjectDiff
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}