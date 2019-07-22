<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkLog
 *
 * @ORM\Table(name="work_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkLogRepository")
 */
class WorkLog
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
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectTask")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;

    /**
     * @var float
     *
     * @ORM\Column(name="logged_hours", type="float")
     */
    private $loggedHours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logged_day", type="date", nullable=true)
     */
    private $loggedDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * Set owner
     *
     * @param User $owner
     *
     * @return WorkLog
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set task
     *
     * @param ProjectTask $task
     *
     * @return WorkLog
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return ProjectTask
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set loggedHours
     *
     * @param integer $loggedHours
     *
     * @return WorkLog
     */
    public function setLoggedHours($loggedHours)
    {
        $this->loggedHours = $loggedHours;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLoggedDay()
    {
        return $this->loggedDay;
    }

    /**
     * @param \DateTime $loggedDay
     */
    public function setLoggedDay($loggedDay)
    {
        $this->loggedDay = $loggedDay;
    }

    /**
     * Get loggedHours
     *
     * @return int
     */
    public function getLoggedHours()
    {
        return $this->loggedHours;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return WorkLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param $originalValue
     * @return float|int|mixed
     */
    public static function getCorrectTimeValue($originalValue)
    {
        $re = '/([0-9.]+)/';
        $re2 = '/([dhmдчм]+)/';

        preg_match($re, $originalValue, $matches);
        $value = current($matches);

        preg_match($re2, $originalValue, $matches);
        $measure = current($matches);

        if ($measure == 'd' || $measure == 'д') {
            $value = $value * 6;
        } elseif ($measure == 'm' || $measure == 'м') {
            $value = $value / 60;
        }

        if ($value && in_array($measure, ['d', 'h', 'm', 'д', 'ч', 'м'])) {
            return $value;
        } else {
            return 0;
        }
    }

    /**
     * @param $value
     * @return string
     */
    public static function getFormattedTime($value)
    {
        $hours = floor($value / 1);
        $time = '';
        if ($hours) {
            $time = $hours . 'ч ';
        }
        $minutes = $value - $hours;
        if ($minutes) {
            $time .= round($minutes * 60) . 'м';
        }

        return $time ?: '0ч';
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRemove(User $user)
    {
        return $this->getOwner()->getId() == $user->getId() &&
        ($this->getCreatedAt()->diff(new \DateTime('now'))->d < 1) &&
        ($this->getCreatedAt()->diff(new \DateTime('now'))->h <= 8);
    }
}

