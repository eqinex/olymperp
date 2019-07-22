<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TechnicalMap
 *
 * @ORM\Table(name="technical_map")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\TechnicalMapRepository")
 */
class TechnicalMap
{
    const TECHNICAL_MAP_STATUS_NOT_APPROVED = 1;
    const TECHNICAL_MAP_STATUS_NEEDS_APPROVE = 2;
    const TECHNICAL_MAP_STATUS_APPROVED = 3;
    const TECHNICAL_MAP_STATUS_NEEDS_FIXING = 4;

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
     * @ORM\Column(name="task", type="string", length=255)
     */
    private $task;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="goal", type="string", length=255)
     */
    private $goal;

    /**
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\TechnicalMapSolutions", mappedBy="technicalMap", cascade="all")
     */
    private $technicalMapSolutions;

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
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\TechnicalMapSignatory", mappedBy="technicalMap", cascade="all")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $signatories;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_title_1", type="string", length=255)
     */
    private $criterionTitle1;

    /**
     * @var int
     *
     * @ORM\Column(name="max_points_1", type="integer", nullable=true)
     */
    private $maxPoints1;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_title_2", type="string", length=255)
     */
    private $criterionTitle2;

    /**
     * @var int
     *
     * @ORM\Column(name="max_points_2", type="integer", nullable=true)
     */
    private $maxPoints2;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_title_3", type="string", length=255, nullable=true)
     */
    private $criterionTitle3;

    /**
     * @var int
     *
     * @ORM\Column(name="max_points_3", type="integer", nullable=true)
     */
    private $maxPoints3;

    /**
     * @var string
     *
     * @ORM\Column(name="criterion_title_4", type="string", length=255, nullable=true)
     */
    private $criterionTitle4;

    /**
     * @var int
     *
     * @ORM\Column(name="max_points_4", type="integer", nullable=true)
     */
    private $maxPoints4;

    /**
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"all"})
     * @ORM\JoinTable(name="technical_map_subscribers",
     *      joinColumns={@ORM\JoinColumn(name="technical_map_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $subscribers;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = self::TECHNICAL_MAP_STATUS_NOT_APPROVED;
        $this->technicalMapSolutions = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return TechnicalMap
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
     * @param string $task
     * @return TechnicalMap
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
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
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * @param string $goal
     * @return TechnicalMap
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
        return $this;
    }

    /**
     * @return TechnicalMapSolutions[]
     */
    public function getTechnicalMapSolutions()
    {
        return $this->technicalMapSolutions;
    }

    /**
     * @param TechnicalMapSolutions[] $technicalMapSolutions
     */
    public function setActivityEvents($technicalMapSolutions)
    {
        $this->technicalMapSolutions = $technicalMapSolutions;
    }

    /**
     * @param User $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
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
     * @return TechnicalMap
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param string $criterionTitle1
     *
     * @return TechnicalMap
     */
    public function setCriterionTitle1($criterionTitle1)
    {
        $this->criterionTitle1 = $criterionTitle1;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterionTitle1()
    {
        return $this->criterionTitle1;
    }

    /**
     * @param string $maxPoints1
     *
     * @return TechnicalMap
     */
    public function setMaxPoints1($maxPoints1)
    {
        $this->maxPoints1 = $maxPoints1;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaxPoints1()
    {
        return $this->maxPoints1;
    }

    /**
     * @param string $criterionTitle2
     *
     * @return TechnicalMap
     */
    public function setCriterionTitle2($criterionTitle2)
    {
        $this->criterionTitle2 = $criterionTitle2;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterionTitle2()
    {
        return $this->criterionTitle2;
    }

    /**
     * @param string $maxPoints2
     *
     * @return TechnicalMap
     */
    public function setMaxPoints2($maxPoints2)
    {
        $this->maxPoints2 = $maxPoints2;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaxPoints2()
    {
        return $this->maxPoints2;
    }

    /**
     * @param string $criterionTitle3
     *
     * @return TechnicalMap
     */
    public function setCriterionTitle3($criterionTitle3)
    {
        $this->criterionTitle3 = $criterionTitle3;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterionTitle3()
    {
        return $this->criterionTitle3;
    }

    /**
     * @param string $maxPoints3
     *
     * @return TechnicalMap
     */
    public function setMaxPoints3($maxPoints3)
    {
        $this->maxPoints3 = $maxPoints3;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaxPoints3()
    {
        return $this->maxPoints3;
    }

    /**
     * @param string $criterionTitle4
     *
     * @return TechnicalMap
     */
    public function setCriterionTitle4($criterionTitle4)
    {
        $this->criterionTitle4 = $criterionTitle4;

        return $this;
    }

    /**
     * @return string
     */
    public function getCriterionTitle4()
    {
        return $this->criterionTitle4;
    }

    /**
     * @param string $maxPoints4
     *
     * @return TechnicalMap
     */
    public function setMaxPoints4($maxPoints4)
    {
        $this->maxPoints4 = $maxPoints4;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaxPoints4()
    {
        return $this->maxPoints4;
    }

    /**
     * @return TechnicalMapSignatory[]
     */
    public function getSignatories()
    {
        return $this->signatories;
    }

    /**
     * @param TechnicalMapSignatory $signatories
     * @return TechnicalMap
     */
    public function setSignatories($signatories)
    {
        $this->signatories = $signatories;
        return $this;
    }

    /**
     * @return array
     */
    public function getSignatoryUsers()
    {
        $users = [];

        foreach ($this->getSignatories() as $signatory) {
            $users[$signatory->getSignatory()->getId()] = $signatory->getSignatory()->getId();
        }

        return $users;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRemoveSignatories(User $user)
    {
        return ($this->isNeedsFixing() || $this->isNeedsApprove() || $this->isApproved()) && $this->isOwner($user);
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::TECHNICAL_MAP_STATUS_NOT_APPROVED => 'technical_map.not_approved',
            self::TECHNICAL_MAP_STATUS_NEEDS_APPROVE => 'technical_map.needs_approve',
            self::TECHNICAL_MAP_STATUS_APPROVED => 'technical_map.approved',
            self::TECHNICAL_MAP_STATUS_NEEDS_FIXING => 'technical_map.needs_fixing'
        ];
    }

    /**
     * @return bool
     */
    public function isNotApproved()
    {
        return $this->status == self::TECHNICAL_MAP_STATUS_NOT_APPROVED;
    }

    /**
     * @return bool
     */
    public function isNeedsApprove()
    {
        return $this->status == self::TECHNICAL_MAP_STATUS_NEEDS_APPROVE;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->status == self::TECHNICAL_MAP_STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isNeedsFixing()
    {
        return $this->status == self::TECHNICAL_MAP_STATUS_NEEDS_FIXING;
    }

    /**
     * @param User $user
     * @return TechnicalMapSignatory|bool
     */
    public function getSignatory(User $user)
    {
        foreach ($this->getSignatories() as $signatory) {
            if ($signatory->getSignatory()->getId() == $user->getId()) {
                return $signatory;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canAddSolution()
    {
        return
                $this->isNotApproved() ||
                $this->isNeedsFixing() ||
                $this->isNeedsApprove()
            ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRequestApprove(User $user)
    {
        return $this->isOwner($user) &&
            (
                $this->isNotApproved() ||
                $this->isNeedsFixing()
            );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canReturnFixing(User $user)
    {
        return ($this->isOwner($user) || $this->getSignatory($user)) && $this->isNeedsApprove();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canApprove(User $user)
    {
        $signatory = $this->getSignatory($user);
        return $this->isNeedsApprove() && $signatory && !$signatory->isApproved();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canDisapprove(User $user)
    {
        $signatory = $this->getSignatory($user);

        return $this->isNeedsApprove() && $signatory && $signatory->isApproved();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkGrants(User $user)
    {
        return $this->isOwner($user) ||
            ($this->getProject() && $this->getProject()->getLeader()->getId() == $user->getId()) ||
            $this->getOwner()->getTeam()->getLeader()->getId() == $user->getId()
            ;
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

    /**
     * @param User $subscriber
     * @return bool
     */
    public function isUserSubscribed(User $subscriber)
    {
        return $this->subscribers->contains($subscriber);
    }
}