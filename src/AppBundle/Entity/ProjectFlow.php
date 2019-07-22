<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectFlow
 *
 * @ORM\Table(name="project_flow")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectFlowRepository")
 */
class ProjectFlow
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
    private $name = '';

    /**
     * Many Flows have Many Stages.
     * @ORM\ManyToMany(targetEntity="ProjectStage")
     * @ORM\JoinTable(name="project_flow_project_stage",
     *      joinColumns={@ORM\JoinColumn(name="project_flow_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_stage_id", referencedColumnName="id", unique=false)}
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
     * @return ProjectFlow
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

