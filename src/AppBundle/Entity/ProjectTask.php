<?php

namespace AppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectTask
 *
 * @ORM\Table(name="project_task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectTaskRepository")
 */
class ProjectTask
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_NEED_APPROVE = 4;
    const STATUS_READY_TO_WORK = 5;
    const STATUS_ON_HOLD = 6;
    const STATUS_NEED_APPROVE_RESULT = 7;
    const STATUS_RESULT_APPROVED = 8;
    const ALL_STATUSES = 9;
    const STATUS_SCHEDULED = 10;

    const TYPE_TASK = 'task';
    const TYPE_EPIC = 'epic';
    const TYPE_MEETING = 'meeting';
    const TYPE_PROTOCOL = 'protocol';

    const SCHEDULER_TYPE_SINGLY = 'singly';
    const SCHEDULER_TYPE_DAILY = 'daily';
    const SCHEDULER_TYPE_WEEKLY = 'weekly';
    const SCHEDULER_TYPE_MONTHLY = 'monthly';
    const SCHEDULER_TYPE_YEARLY = 'yearly';

    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_BIG = 3;

    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

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
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = "";

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = "task";

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject = "";

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = "";

    /**
     * @ORM\ManyToOne(targetEntity="ProjectStage")
     * @ORM\JoinColumn(name="project_stage_id", referencedColumnName="id")
     */
    private $projectStage;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaskSiblings")
     * @ORM\JoinColumn(name="task_siblings_id", referencedColumnName="id")
     */
    private $taskSiblings;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectTask")
     * @ORM\JoinColumn(name="epic_id", referencedColumnName="id")
     */
    private $epic;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectTask")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id")
     */
    private $protocol;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", name="end_at", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="datetime", name="closed_at", nullable=true)
     */
    private $closedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     */
    private $responsibleUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="controlling_user_id", referencedColumnName="id")
     */
    private $controllingUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     */
    private $reporter;

    /**
     * @var float
     *
     * @ORM\Column(name="original_estimate", type="float",)
     */
    private $originalEstimate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="remaining_estimate", type="float",)
     */
    private $remainingEstimate = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="time_spent", type="float",)
     */
    private $timeSpent = 0;

    /**
     * @ORM\OneToMany(targetEntity="WorkLog", mappedBy="task", cascade="all")
     */
    private $workLogs;

    /**
     * @ORM\OneToMany(targetEntity="TaskFile", mappedBy="task", cascade="all")
     */
    private $attachments;

    /**
     * @ORM\OneToMany(targetEntity="TaskComment", mappedBy="task", cascade="all")
     */
    private $taskComments;

    /**
     * @ORM\OneToMany(targetEntity="ProjectTask", mappedBy="epic", cascade="all")
     */
    private $epicTasks;

    /**
     * @ORM\OneToMany(targetEntity="ProjectTask", mappedBy="protocol", cascade="all")
     */
    private $protocolTasks;

    /**
     * Many Flows have Many Stages.
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"all"})
     * @ORM\JoinTable(name="tasks_subscribers",
     *      joinColumns={@ORM\JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="ProtocolMembers", mappedBy="protocol", cascade="all")
     */
    private $protocolMembers;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaskResult")
     * @ORM\JoinColumn(name="result", referencedColumnName="id")
     */
    private $result;

    /**
     * @var bool
     *
     * @ORM\Column(name="scheduler", type="boolean", nullable=true, options={"default":"0"})
     */
    private $scheduler = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="scheduled_period", type="string", length=255, nullable=true)
     */
    private $scheduledPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="daysWeek", type="text", nullable=true)
     */
    private $daysWeek;

    /**
     * @var string
     *
     * @ORM\Column(name="number_of_return", type="string", nullable=true)
     */
    private $numberOfReturn;

    /**
     * ProjectTask constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->type = self::TYPE_TASK;

        $this->subscribers = new ArrayCollection();
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ProjectTask
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return ProjectTask
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
    public function getEpic()
    {
        return $this->epic;
    }

    /**
     * @param mixed $epic
     */
    public function setEpic($epic)
    {
        $this->epic = $epic;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param $project
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param $startAt
     * @return $this
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getScheduledPeriod()
    {
        return $this->scheduledPeriod;
    }

    /**
     * @param string $scheduledPeriod
     * @return ProjectTask
     */
    public function setScheduledPeriod($scheduledPeriod)
    {
        $this->scheduledPeriod = $scheduledPeriod;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isScheduler()
    {
        return $this->scheduler;
    }

    /**
     * @param boolean $scheduler
     * @return ProjectTask
     */
    public function setScheduler($scheduler)
    {
        $this->scheduler = $scheduler;
        return $this;
    }

    /**
     * @return array
     */
    public function getDaysWeek()
    {
        return json_decode($this->daysWeek);
    }

    /**
     * @param string $daysWeek
     * @return ProjectTask
     */
    public function setDaysWeek($daysWeek)
    {
        $this->daysWeek = $daysWeek;
        return $this;
    }

    /**
     * Get numberOfReturn
     *
     * @return string
     */
    public function getNumberOfReturn()
    {
        return $this->numberOfReturn;
    }

    /**
     * Set numberOfReturn
     *
     * @param string $numberOfReturn
     *
     * @return string
     */
    public function setNumberOfReturn($numberOfReturn)
    {
        $this->numberOfReturn = $numberOfReturn;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param $endAt
     * @return $this
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @param \DateTime $closedAt
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;
    }

    /**
     * @return User
     */
    public function getResponsibleUser()
    {
        return $this->responsibleUser;
    }

    /**
     * @param User $responsibleUser
     */
    public function setResponsibleUser($responsibleUser)
    {
        $this->responsibleUser = $responsibleUser;
    }

    /**
     * @return User
     */
    public function getControllingUser()
    {
        return $this->controllingUser;
    }

    /**
     * @param User $controllingUser
     * @return ProjectTask
     */
    public function setControllingUser($controllingUser)
    {
        $this->controllingUser = $controllingUser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param $reporter
     * @return $this
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;
        return $this;
    }

    /**
     * @return float
     */
    public function getOriginalEstimate()
    {
        return $this->originalEstimate;
    }

    /**
     * @param float $originalEstimate
     * @return ProjectTask
     */
    public function setOriginalEstimate($originalEstimate)
    {
        $value = WorkLog::getCorrectTimeValue($originalEstimate);

        if ($value) {
            $this->originalEstimate = $value;
            $this->setRemainingEstimate($value);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * @param float $timeSpent
     * @return ProjectTask
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
        return $this;
    }

    /**
     * @param float $timeSpent
     * @return ProjectTask
     */
    public function logTimeSpent($timeSpent)
    {
        $this->timeSpent += $timeSpent;
        $this->writeOffRemainingEstimate($timeSpent);

        return $this;
    }

    /**
     * @param $time
     * @return ProjectTask
     */
    public function removeWorkLog($time)
    {
        $this->timeSpent -= $time;

        $this->setRemainingEstimate();

        return $this;
    }

    /**
     * @return WorkLog[]
     */
    public function getWorkLogs()
    {
        return $this->workLogs;
    }

    /**
     * @param WorkLog[] $workLogs
     * @return ProjectTask
     */
    public function setWorkLogs($workLogs)
    {
        $this->workLogs = $workLogs;
        return $this;
    }

    /**
     * @return int
     */
    public function getRemainingEstimate()
    {
        return $this->remainingEstimate;
    }

    /**
     * @return mixed
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param mixed $attachments
     * @return ProjectTask
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEpicTasks()
    {
        return $this->epicTasks;
    }

    /**
     * @param mixed $epicTasks
     * @return ProjectTask
     */
    public function setEpicTasks($epicTasks)
    {
        $this->epicTasks = $epicTasks;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param mixed $subscribers
     */
    public function setSubscribers($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * @param User $subscriber
     * @return $this
     */
    public function addSubscriber($subscriber)
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers->add($subscriber);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaskSiblings()
    {
        return $this->taskSiblings;
    }

    /**
     * @param mixed $taskSiblings
     * @return ProjectTask
     */
    public function setTaskSiblings($taskSiblings)
    {
        $this->taskSiblings = $taskSiblings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return ProjectTask
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     * @return ProjectTask
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtocolTasks()
    {
        return $this->protocolTasks;
    }

    /**
     * @param mixed $protocolTasks
     * @return ProjectTask
     */
    public function setProtocolTasks($protocolTasks)
    {
        $this->protocolTasks = $protocolTasks;
        return $this;
    }

    /**
     * @return ProtocolMembers[]
     */
    public function getProtocolMembers()
    {
        return $this->protocolMembers;
    }

    /**
     * @return TaskComment[]
     */
    public function getTaskComments()
    {
        return $this->taskComments;
    }

    /**
     * @param ProtocolMembers $protocolMembers
     * @return ProjectTask
     */
    public function setProtocolMembers($protocolMembers)
    {
        $this->protocolMembers = $protocolMembers;
        return $this;
    }

    /**
     * @param User $subscriber
     * @return $this
     */
    public function removeSubscriber($subscriber)
    {
        if ($this->subscribers->contains($subscriber)) {
            $this->subscribers->removeElement($subscriber);
        }

        return $this;
    }

    public function isUserSubscribed(User $subscriber)
    {
        return $this->subscribers->contains($subscriber);
    }

    /**
     * @return ProjectTask
     */
    public function setRemainingEstimate()
    {
        if (($this->getOriginalEstimate() - $this->getTimeSpent()) > 0) {
            $this->remainingEstimate = $this->getOriginalEstimate() - $this->getTimeSpent();
        } else {
            $this->remainingEstimate = 0;
        }

        return $this;
    }

    protected function writeOffRemainingEstimate($spentTime)
    {
        if (($this->remainingEstimate - $spentTime) > 0) {
            $this->remainingEstimate = $this->remainingEstimate - $spentTime;
        } else {
            $this->remainingEstimate = 0;
        }
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => 'new',
            self::STATUS_IN_PROGRESS => 'in_progress',
            self::STATUS_DONE => 'done',
            self::STATUS_CANCELLED => 'cancelled',
            self::STATUS_NEED_APPROVE => 'need_approve',
            self::STATUS_READY_TO_WORK => 'ready_to_work',
            self::STATUS_ON_HOLD => 'on_hold',
            self::STATUS_NEED_APPROVE_RESULT => 'need_approve_result',
            self::STATUS_RESULT_APPROVED => 'result_approved'
        ];
    }

    /**
     * @return array
     */
    public static function getSchedulerStatusList()
    {
        return [
            self::STATUS_DONE => 'done',
            self::STATUS_CANCELLED => 'cancelled',
            self::STATUS_SCHEDULED => 'scheduled',
            self::ALL_STATUSES => 'all_statuses'
        ];
    }

    /**
     * @return array
     */
    public static function getDaysOfTheWeekList()
    {
        return [
            self::MONDAY => 'monday',
            self::TUESDAY => 'tuesday',
            self::WEDNESDAY => 'wednesday',
            self::THURSDAY => 'thursday',
            self::FRIDAY => 'friday',
            self::SATURDAY => 'saturday',
            self::SUNDAY => 'sunday',
        ];
    }

    /**
     * @return string
     */
    public function getCurrentStatusName()
    {
        return self::getStatusList()[$this->status];
    }

    /**
     * @return array
     */
    public static function getPriorityList()
    {
        return [
            self::PRIORITY_LOW => 'low',
            self::PRIORITY_NORMAL => 'normal',
            self::PRIORITY_HIGH => 'high',
            self::PRIORITY_BIG => 'big',
        ];
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_TASK => self::TYPE_TASK,
            self::TYPE_EPIC => self::TYPE_EPIC,
            self::TYPE_MEETING => self::TYPE_MEETING,
        ];
    }

    /**
     * @return array
     */
    public static function getSchedulerTypesList()
    {
        return [
            self::SCHEDULER_TYPE_SINGLY => self::SCHEDULER_TYPE_SINGLY,
            self::SCHEDULER_TYPE_DAILY => self::SCHEDULER_TYPE_DAILY,
            self::SCHEDULER_TYPE_WEEKLY => self::SCHEDULER_TYPE_WEEKLY,
            self::SCHEDULER_TYPE_MONTHLY => self::SCHEDULER_TYPE_MONTHLY,
            self::SCHEDULER_TYPE_YEARLY => self::SCHEDULER_TYPE_YEARLY
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return array
     */
    public function getPriorityLabels()
    {
        return [
            0 => 'success',
            1 => 'primary',
            2 => 'warning',
            3 => 'danger',
            4 => 'inverse',
            5 => 'success',
            6 => 'warning',
            7 => 'inverse',
            8 => 'success',
            10 => 'success'
        ];
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $now = new \DateTime();
        if ($this->getEndAt() < $this->getClosedAt() || ($this->getClosedAt() == null && $this->getEndAt() < $now)){
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return in_array(
            $this->status,
            [
                self::STATUS_NEW,
                self::STATUS_IN_PROGRESS,
                self::STATUS_NEED_APPROVE,
                self::STATUS_NEED_APPROVE_RESULT,
                self::STATUS_READY_TO_WORK,
                self::STATUS_ON_HOLD,
                self::STATUS_DONE
            ]
        );
    }

    /**
     * @param $value
     * @return string
     */
    public static function getFormattedTime($value)
    {
        return WorkLog::getFormattedTime($value);
    }

    public function canCancelTask(User $user)
    {
        return (!in_array($this->getStatus(), [self::STATUS_CANCELLED, self::STATUS_DONE])) &&
            (
                $this->getResponsibleUser()->getId() == $user->getId() ||
                $this->getReporter()->getId() == $user->getId() ||
                $user->canCancelAllTasks()
            )
        ;
    }


    /**
     * @return array
     */
    public function getEpicWorkLogs()
    {
        $epicWorklogs = [];

        if ($this->getType() == ProjectTask::TYPE_EPIC) {
            foreach ($this->getEpicTasks() as $eTask) {
                $epicWorklogs[$eTask->getId()] = [
                    'id' => $eTask->getId(),
                    'title' => $eTask->getTitle(),
                    'worklogs' => []
                ];

                foreach ($eTask->getWorkLogs() as $workLog) {
                    $epicWorklogs[$eTask->getId()]['worklogs'][$workLog->getOwner()->getId()]['name'] =
                        $workLog->getOwner()->getLastNameWithInitials();

                    if (!isset($epicWorklogs[$eTask->getId()]['worklogs'][$workLog->getOwner()->getId()]['hours'])) {
                        $epicWorklogs[$eTask->getId()]['worklogs'][$workLog->getOwner()->getId()]['hours'] = 0;
                    }

                    $epicWorklogs[$eTask->getId()]['worklogs'][$workLog->getOwner()->getId()]['hours'] += $workLog->getLoggedHours();
                }
            }
        }

        return $epicWorklogs;
    }

    /**
     * @return array
     */
    public function getEpicEstimateTime()
    {
        $epicEstimateTime = [];
        $originalEstimateEpic = 0;
        $remainingEstimateEpic = 0;
        $timeSpentEpic = 0;

        if ($this->getType() == ProjectTask::TYPE_EPIC) {
            foreach ($this->getEpicTasks() as $eTask) {
                /** @var ProjectTask $eTask */
                if ($eTask->getStatus() != ProjectTask::STATUS_CANCELLED || $eTask->getTimeSpent() != 0) {
                    $originalEstimateEpic = $originalEstimateEpic + $eTask->getOriginalEstimate();
                    $remainingEstimateEpic = $remainingEstimateEpic + $eTask->getRemainingEstimate();
                    $timeSpentEpic = $timeSpentEpic + $eTask->getTimeSpent();
                }
            }
            $epicEstimateTime = [
                'originalEstimateEpic' => $originalEstimateEpic,
                'remainingEstimateEpic' => $remainingEstimateEpic,
                'timeSpentEpic' => $timeSpentEpic,
            ];
        }

         return $epicEstimateTime;
    }

    /**
     * @return array
     */
    public function getProtocolMember()
    {
        $users = [];

        foreach ($this->getProtocolMembers() as $member) {
            $users[$member->getMember()->getId()] = $member->getMember()->getId();
        }

        return $users;
    }

    /**
     * @param $responsibleUser
     * @return array
     */
    public function getResponsibleUserAttachments($responsibleUser)
    {
        $attachments = [];

        foreach ($this->getAttachments() as $attachment) {
            /** @var TaskFile $attachment */
            if ($attachment->getOwner() == $responsibleUser) {
                $attachments[] = $attachment->getFileName();
            }
        }

        return $attachments;
    }

    /**
     * @param $responsibleUser
     * @return string
     */
    public function getResponsibleUserLastComment($responsibleUser)
    {
        $lastComment = [];

        foreach ($this->getTaskComments() as $taskComment) {
            /** @var TaskComment $taskComment */
            if ($taskComment->getOwner() == $responsibleUser) {
                $lastComment[] = $taskComment->getCommentText();
            }
        }

        return end($lastComment);
    }

    public function incrementNumberOfReturn()
    {
        $this->numberOfReturn += 1;

    }
}

