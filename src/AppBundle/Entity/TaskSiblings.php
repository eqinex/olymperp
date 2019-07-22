<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStatus
 *
 * @ORM\Table(name="task_siblings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskSiblingsRepository")
 */
class TaskSiblings
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
     * @ORM\OneToMany(targetEntity="ProjectTask", mappedBy="taskSiblings", cascade="all")
     */
    private $siblings;

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
     * @return mixed
     */
    public function getSiblings()
    {
        return $this->siblings;
    }

    /**
     * @param mixed $siblings
     * @return TaskSiblings
     */
    public function setSiblings($siblings)
    {
        $this->siblings = $siblings;
        return $this;
    }
}

