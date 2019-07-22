<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStageProgress
 *
 * @ORM\Table(name="project_stage_progress")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectStageProgressRepository")
 */
class ProjectStageProgress
{
    const STATE_NOT_STARTED = 'work_not_started';
    const STATE_STARTED = 'work_started';
    const STATE_STOPPED = 'work_stopped';
    const STATE_DONE = 'work_done';

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
     * @ORM\Column(name="status", type="string", length=255, unique=false)
     */
    private $status = "work_not_started";

    /**
     * @ORM\ManyToOne(targetEntity="ProjectStage")
     * @ORM\JoinColumn(name="project_stage_id", referencedColumnName="id")
     */
    private $projectStage;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", name="end_at", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     */
    private $responsibleUser;

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getProjectStage()
    {
        return $this->projectStage;
    }

    /**
     * @param mixed $projectStage
     */
    public function setProjectStage($projectStage)
    {
        $this->projectStage = $projectStage;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param mixed $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return mixed
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endAt
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return mixed
     */
    public function getResponsibleUser()
    {
        return $this->responsibleUser;
    }

    /**
     * @param mixed $responsibleUser
     */
    public function setResponsibleUser($responsibleUser)
    {
        $this->responsibleUser = $responsibleUser;
    }

    /**
     * @return array
     */
    public function getStates()
    {
        return [
            self::STATE_NOT_STARTED,
            self::STATE_STARTED,
            self::STATE_STOPPED,
            self::STATE_DONE
        ];
    }
}

