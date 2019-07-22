<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskFile
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 */
class TaskFile extends File
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectTask")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     * @return TaskFile
     */
    public function setTask($task)
    {
        $this->task = $task;
        return $this;
    }
}

