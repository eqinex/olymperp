<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
{
    const TYPE_NOT_DEFINED = 'not_defined';
    const TYPE_PURCHASE_DEPARTMENT = 'purchasing_team';
    const TYPE_FINANCIAL_DEPARTMENT = 'financial_team';
    const TYPE_GENERAL_SERVICE = 'general_team';

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
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id")
     */
    private $leader;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="deputy_leader_id", referencedColumnName="id")
     */
    private $deputyLeader;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = '';


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="team", cascade="all")
     */
    private $teamMembers;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="department", type="boolean")
     */
    private $department = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_purchase_id", type="integer")
     */
    private $lastPurchaseId = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="purchases_team", type="boolean")
     */
    private $purchasesTeam = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="financial_team", type="boolean")
     */
    private $financialTeam = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="production_team", type="boolean")
     */
    private $productionTeam = false;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="needs_task_approve", type="boolean")
     */
    private $needsTaskApprove = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="needs_result_approve", type="boolean")
     */
    private $needsResultApprove = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="needs_team_leader_notification", type="boolean")
     */
    private $needsTeamLeaderNotification = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="include_submission_teams", type="boolean")
     */
    private $includeSubmissionTeams = false;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_chat_id", type="string", length=255, unique=true, nullable=true)
     */
    private $telegramChatId;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="parentTeam")
     */
    private $childTeams;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="parent_team_id", referencedColumnName="id")
     */
    private $parentTeam;


    public function __construct()
    {
        $this->teamMembers = new ArrayCollection();
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
     * Set leader
     *
     * @param User $leader
     *
     * @return Team
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     *
     * @return User
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set deputyLeader
     *
     * @param User $deputyLeader
     *
     * @return Team
     */
    public function setDeputyLeader($deputyLeader)
    {
        $this->deputyLeader = $deputyLeader;

        return $this;
    }

    /**
     * Get deputyLeader
     *
     * @return User
     */
    public function getDeputyLeader()
    {
        return $this->deputyLeader;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Team
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return User[]
     */
    public function getTeamMembers()
    {
        return $this->teamMembers;
    }

    /**
     * @return User[]
     */
    public function getAllTeamMembers()
    {
        $team = [];
        if ($this->getLeader() && !$this->getLeader()->isAdmin()) {
            $team[$this->getLeader()->getId()] = $this->getLeader();
        }

        foreach ($this->getTeamMembers() as $member) {
            if (!$member->isAdmin()) {
                $team[$member->getId()] = $member;
            }
        }

        if ($this->isIncludeSubmissionTeams()) {
            foreach ($this->getChildTeams() as $sTeam) {
                foreach ($sTeam->getTeamMembers() as $member) {
                    if (!$member->isAdmin()) {
                        $team[$member->getId()] = $member;
                    }
                }
            }
        }

        return $team;
    }

    /**
     * @param User[] $teamMembers
     * @return Team
     */
    public function setTeamMembers($teamMembers)
    {
        $this->teamMembers = $teamMembers;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Team
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastPurchaseId()
    {
        return $this->lastPurchaseId;
    }

    /**
     * @param int $lastPurchaseId
     * @return Team
     */
    public function setLastPurchaseId($lastPurchaseId)
    {
        $this->lastPurchaseId = $lastPurchaseId;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPurchasesTeam()
    {
        return $this->purchasesTeam;
    }

    /**
     * @param boolean $purchasesTeam
     * @return Team
     */
    public function setPurchasesTeam($purchasesTeam)
    {
        $this->purchasesTeam = $purchasesTeam;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDepartment()
    {
        return $this->department;
    }

    /**
     * @param boolean $department
     * @return Team
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFinancialTeam()
    {
        return $this->financialTeam;
    }

    /**
     * @param boolean $financialTeam
     * @return Team
     */
    public function setFinancialTeam($financialTeam)
    {
        $this->financialTeam = $financialTeam;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isProductionTeam()
    {
        return $this->productionTeam;
    }

    /**
     * @param boolean $productionTeam
     * @return Team
     */
    public function setProductionTeam($productionTeam)
    {
        $this->productionTeam = $productionTeam;
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
     * @return Team
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNeedsTaskApprove()
    {
        return $this->needsTaskApprove;
    }

    /**
     * @param boolean $needsTaskApprove
     */
    public function setNeedsTaskApprove($needsTaskApprove)
    {
        $this->needsTaskApprove = $needsTaskApprove;
    }

    /**
     * @return boolean
     */
    public function isNeedsResultApprove()
    {
        return $this->needsResultApprove;
    }

    /**
     * @param boolean $needsResultApprove
     */
    public function setNeedsResultApprove($needsResultApprove)
    {
        $this->needsResultApprove = $needsResultApprove;
    }

    /**
     * @return string
     */
    public function getTelegramChatId()
    {
        return $this->telegramChatId;
    }

    /**
     * @param string $telegramChatId
     * @return Team
     */
    public function setTelegramChatId($telegramChatId)
    {
        $this->telegramChatId = $telegramChatId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildTeams()
    {
        return $this->childTeams;
    }

    /**
     * @param mixed $childTeams
     * @return Team
     */
    public function setChildTeams($childTeams)
    {
        $this->childTeams = $childTeams;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentTeam()
    {
        return $this->parentTeam;
    }

    /**
     * @param mixed $parentTeam
     * @return Team
     */
    public function setParentTeam($parentTeam)
    {
        $this->parentTeam = $parentTeam;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNeedsTeamLeaderNotification()
    {
        return $this->needsTeamLeaderNotification;
    }

    /**
     * @param boolean $needsTeamLeaderNotification
     * @return Team
     */
    public function setNeedsTeamLeaderNotification($needsTeamLeaderNotification)
    {
        $this->needsTeamLeaderNotification = $needsTeamLeaderNotification;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isIncludeSubmissionTeams()
    {
        return $this->includeSubmissionTeams;
    }

    /**
     * @param boolean $includeSubmissionTeams
     * @return Team
     */
    public function setIncludeSubmissionTeams($includeSubmissionTeams)
    {
        $this->includeSubmissionTeams = $includeSubmissionTeams;
        return $this;
    }

    /**
     * @param User $user
     * @return array
     */
    public function isUserPartOfTeam(User $user)
    {
        if ($this->getLeader()->getId() == $user->getId()) {
            return true;
        }

        foreach ($this->getTeamMembers() as $member) {
            if ($member->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function isPurchaseLeader(User $user)
    {
        if ($this->isTeamLeader($user) && $this->isPurchasesTeam()){
            return true;
        }

        return false;
    }
    
    /**
     * @param User $user
     * @return boolean
     */
    public function isTeamLeader(User $user)
    {
        if ($this->getLeader()->getId() == $user->getId() || ($this->getDeputyLeader() != null && $this->getDeputyLeader()->getId() == $user->getId()) ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_NOT_DEFINED => self::TYPE_NOT_DEFINED,
            self::TYPE_PURCHASE_DEPARTMENT => self::TYPE_PURCHASE_DEPARTMENT,
            self::TYPE_FINANCIAL_DEPARTMENT => self::TYPE_FINANCIAL_DEPARTMENT,
            self::TYPE_GENERAL_SERVICE => self::TYPE_GENERAL_SERVICE
        ];
    }
}