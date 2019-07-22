<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectCategory
 *
 * @ORM\Table(name="project_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectCategoryRepository")
 */
class ProjectCategory
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id")
     */
    private $leader;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectFlow")
     * @ORM\JoinColumn(name="project_flow_id", referencedColumnName="id")
     */
    private $projectFlow;

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
     * @return ProjectCategory
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
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param mixed $leader
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;
    }

    /**
     * @return mixed
     */
    public function getProjectFlow()
    {
        return $this->projectFlow;
    }

    /**
     * @param mixed $projectFlow
     */
    public function setProjectFlow($projectFlow)
    {
        $this->projectFlow = $projectFlow;
    }

    public function __toString()
    {
        return $this->name;
    }
}

