<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\ActivityRepository")
 */
class Activity
{
    const CURRENT_PROJECTS = 'current_projects';
    const PRE_CONTRACTUAL_PROJECTS = 'pre_contractual_projects';
    const OTHER_ACTIVITIES = 'other_activities';
    const DEFERRED_PROJECTS = 'deferred_projects';

    const ACTIVITY_RESULT_NOT_PERFORMED = 1;
    const ACTIVITY_RESULT_DONE = 2;

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
     * @ORM\Column(name="code", type="string", length=255, unique=true, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="activity", type="string", length=255, nullable=true)
     */
    private $activity;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="profitability", type="string", length=255)
     */
    private $profitability;

    /**
     * @var float
     *
     * @ORM\Column(name="plan", type="float", nullable=true)
     */
    private $plan;

    /**
     * @var boolean
     *
     * @ORM\Column(name="high_risk", type="boolean", nullable=true)
     */
    private $highRisk;

    /**
     * @var float
     *
     * @ORM\Column(name="fact", type="float", nullable=true)
     */
    private $fact;

    /**
     * @var float
     *
     * @ORM\Column(name="received", type="float", nullable=true)
     */
    private $received;

    /**
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\ActivityEvents", mappedBy="activity", cascade="all")
     */
    private $activityEvents;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=true)
     */
    private $result;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     */
    private $responsibleUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * Activity constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->activityEvents = new ArrayCollection();
        $this->result = self::ACTIVITY_RESULT_NOT_PERFORMED;
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
     * Set code
     *
     * @param string $code
     *
     * @return Activity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get activity
     *
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set activity
     * @param string $activity
     * @return Activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     * @param string $category
     * @return Activity
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get profitability
     *
     * @return string
     */
    public function getProfitability()
    {
        return $this->profitability;
    }

    /**
     * Set profitability
     * @param string $profitability
     * @return Activity
     */
    public function setProfitability($profitability)
    {
        $this->profitability = $profitability;
        return $this;
    }

    /**
     * Set plan
     * @param float $plan
     * @return Activity
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     * @return float
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set highRisk
     * @param boolean $highRisk
     * @return Activity
     */
    public function setHighRisk($highRisk)
    {
        $this->highRisk = $highRisk;

        return $this;
    }

    /**
     * Get high_risk
     * @return boolean
     */
    public function getHighRisk()
    {
        return $this->highRisk;
    }

    /**
     * Set fact
     * @param float $fact
     * @return Activity
     */
    public function setFact($fact)
    {
        $this->fact = $fact;

        return $this;
    }

    /**
     * Get fact
     * @return float
     */
    public function getFact()
    {
        return $this->fact;
    }

    /**
     * Set received
     * @param float $received
     * @return Activity
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get received
     * @return float
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * @return ActivityEvents[]
     */
    public function getActivityEvents()
    {
        return $this->activityEvents;
    }

    /**
     * @param ActivityEvents[] $activityEvents
     */
    public function setActivityEvents($activityEvents)
    {
        $this->activityEvents = $activityEvents;
    }

    /**
     * Get result
     *
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set result
     * @param integer $result
     * @return Activity
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Set responsibleUser
     *
     * @param User $responsibleUser
     *
     * @return $this
     */
    public function setResponsibleUser($responsibleUser)
    {
        $this->responsibleUser = $responsibleUser;

        return $this;
    }

    /**
     * Get responsibleUser
     *
     * @return User
     */
    public function getResponsibleUser()
    {
        return $this->responsibleUser;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isResponsibleUser(User $user)
    {
        return $this->responsibleUser->getId() == $user->getId();
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return $this
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
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->owner->getId() == $user->getId();
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
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
     * @return Activity
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
     * @param \DateTime $endAt
     * @return Activity
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNotPerformed()
    {
        return $this->result == self::ACTIVITY_RESULT_NOT_PERFORMED;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->result == self::ACTIVITY_RESULT_DONE;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canEditActivity(User $user)
    {
        return ($this->isOwner($user) || $user->canViewAllActivity() || $this->isResponsibleUser($user)) && ($this->isNotPerformed());
    }

    /**
     * @return array
     */
    public static function getCategoriesList()
    {
        return [
            self::CURRENT_PROJECTS => self::CURRENT_PROJECTS,
            self::PRE_CONTRACTUAL_PROJECTS => self::PRE_CONTRACTUAL_PROJECTS,
            self::OTHER_ACTIVITIES => self::OTHER_ACTIVITIES,
            self::DEFERRED_PROJECTS => self::DEFERRED_PROJECTS,
        ];
    }

    /**
     * @return array
     */
    public static function getResultsList()
    {
        return [
            self::ACTIVITY_RESULT_NOT_PERFORMED => 'activity.result_not_performed',
            self::ACTIVITY_RESULT_DONE => 'activity.result_done'
        ];
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkGrants(User $user)
    {
        return $user->canViewAllActivity() ||
            $this->getOwner()->getId() == $user->getId() ||
            $this->getResponsibleUser()->getId() == $user->getId();
    }
}