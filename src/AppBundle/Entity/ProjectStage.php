<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStage
 *
 * @ORM\Table(name="project_stage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectStageRepository")
 */
class ProjectStage
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
     * @ORM\Column(name="name", type="text")
     */
    private $name = "";

    /**
     * Many Project have Many Stages.
     * @ORM\ManyToMany(targetEntity="ProjectStage")
     * @ORM\JoinTable(name="project_stage_project_stages",
     *      joinColumns={@ORM\JoinColumn(name="project_stage_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_stages_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $projectStages;

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
     * @return ProjectStage
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
    public function getProjectStages()
    {
        return $this->projectStages;
    }

    /**
     * @param mixed $projectStages
     */
    public function setProjectStages($projectStages)
    {
        $this->projectStages = $projectStages;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}

