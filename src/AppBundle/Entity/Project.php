<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\PurchaseConstants;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"project" = "Project", "product" = "Product", "team" = "TeamSpace" })
 */
class Project
{
    const PRIORITY_A_PLUS = 1;
    const PRIORITY_A = 2;
    const PRIORITY_B = 3;
    const PRIORITY_C = 4;

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name = "";

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code = "";

    /**
     * @var string
     *
     * @ORM\Column(name="goal", type="text", nullable=true)
     */
    private $goal = "";

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = "project";

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = "";

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id")
     */
    private $leader;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="purchasing_manager", referencedColumnName="id")
     */
    private $purchasingManager;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectStatus")
     * @ORM\JoinColumn(name="project_status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="ProjectMember", mappedBy="project", cascade="all")
     */
    private $projectMembers;

    /**
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", name="end_at", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\OneToMany(targetEntity="ProjectStageProgress", mappedBy="project", cascade="all")
     */
    private $projectStages;

    /**
     * @ORM\OneToMany(targetEntity="ProjectTask", mappedBy="project", cascade="all")
     */
    private $projectTasks;

    /**
     * @ORM\OneToMany(targetEntity="PurchaseBundle\Entity\PurchaseRequest", mappedBy="project", cascade="all")
     */
    private $projectPurchases;

    /**
     * @ORM\OneToMany(targetEntity="ProductionBundle\Entity\Ware", mappedBy="project", cascade="all")
     */
    private $wares;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_chat_id", type="string", length=255, unique=true, nullable=true)
     */
    private $telegramChatId;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_chat_url", type="string", length=255, unique=true, nullable=true)
     */
    private $telegramChatUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    public function __construct()
    {
        $this->projectMembers = new ArrayCollection();
        $this->projectStages = new ArrayCollection();
        $this->projectPurchases = new ArrayCollection();
        $this->startAt = new \DateTime();
        $this->endAt = new \DateTime();
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
     * Set text
     *
     * @param string $name
     *
     * @return Project
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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Project
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return Project
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     * @return Project
     */
    public function setTeam($team)
    {
        if ($this->getType() == 'team') {
            $this->team = $team;
        } else {
            $this->team = null;
        }

        return $this;
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
     *
     * @return $this
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurchasingManager()
    {
        return $this->purchasingManager;
    }

    /**
     * @param mixed $purchasingManager
     * @return Project
     */
    public function setPurchasingManager($purchasingManager)
    {
        $this->purchasingManager = $purchasingManager;
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
     * @param \DateTime $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
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
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return ProjectCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param ProjectCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return ArrayCollection|ProjectMember[]
     */
    public function getProjectMembers()
    {
        return $this->projectMembers;
    }

    /**
     * @param mixed $projectMembers
     */
    public function setProjectMembers($projectMembers)
    {
        $this->projectMembers = $projectMembers;
    }

    /**
     * @param ProjectMember $projectMember
     */
    public function addProjectMember(ProjectMember $projectMember)
    {
        $this->getProjectMembers()->add($projectMember);
        $projectMember->setProject($this);
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
    }

    /**
     * @return ProjectStageProgress[]
     */
    public function getProjectStages()
    {
        return $this->projectStages;
    }

    /**
     * @param ProjectStageProgress[] $projectStages
     */
    public function setProjectStages($projectStages)
    {
        $this->projectStages = $projectStages;
    }

    /**
     * @return ProjectTask[]
     */
    public function getProjectTasks()
    {
        return $this->projectTasks;
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
     */
    public function setTelegramChatId($telegramChatId)
    {
        $this->telegramChatId = $telegramChatId;
    }

    /**
     * @return string
     */
    public function getTelegramChatUrl()
    {
        return $this->telegramChatUrl;
    }

    /**
     * @param string $telegramChatUrl
     * @return Project
     */
    public function setTelegramChatUrl($telegramChatUrl)
    {
        $this->telegramChatUrl = $telegramChatUrl;
        return $this;
    }

    /**
     * @param mixed $projectTasks
     */
    public function setProjectTasks($projectTasks)
    {
        $this->projectTasks = $projectTasks;
    }

    /**
     * @return PurchaseRequest[]
     */
    public function getProjectPurchases()
    {
        return $this->projectPurchases;
    }

    /**
     * @param PurchaseRequest $projectPurchases
     * @return Project
     */
    public function setProjectPurchases($projectPurchases)
    {
        $this->projectPurchases = $projectPurchases;
        return $this;
    }

    /**
     * @return Ware
     */
    public function getWares()
    {
        return $this->wares;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     *
     * @return string
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @return Project
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param mixed $supplier
     * @return Project
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getActiveTasksCount()
    {
        $activeTasks = 0;

        foreach ($this->getProjectTasks() as $task) {
            if ($task->getType() != ProjectTask::TYPE_PROTOCOL and in_array($task->getStatus(), [
                    ProjectTask::STATUS_NEW,
                    ProjectTask::STATUS_IN_PROGRESS,
                    ProjectTask::STATUS_NEED_APPROVE,
                    ProjectTask::STATUS_READY_TO_WORK,
                    ProjectTask::STATUS_ON_HOLD,
                    ProjectTask::STATUS_NEED_APPROVE_RESULT,
                    ])) {
                $activeTasks++;
            }
        }

        return $activeTasks;
    }

    /**
     * @return int
     */
    public function getActiveProjectPurchases()
    {
        $activePurchases = 0;

        foreach ($this->getProjectPurchases() as $projectPurchase) {
            if (!in_array($projectPurchase->getStatus(), [PurchaseConstants::STATUS_REJECTED])) {
                $activePurchases++;
            }
        }

        return $activePurchases;
    }

    /**
     * @return int
     */
    public function getDaysPassed()
    {
        if ($this->endAt instanceof \DateTime) {
            $totalDays = $this->endAt->diff($this->getStartAt())->format('%a');
            return round(($this->startAt->diff(new \DateTime())->format('%a') / $totalDays), 2) * 100;
        } else {
            return 0;
        }
    }

    /**
     * @param int $projectStageId
     * @return mixed|null
     */
    public function findProjectStage($projectStageId)
    {
        foreach ($this->projectStages as $stage) {
            if ($stage->getProjectStage()->getId() == $projectStageId) {
                return $stage;
            }
        }

        return false;
    }

    /**
     * @param bool $includeSubmissionTeam
     * @param User $user
     * @return array
     */
    public function getProjectMembersTeams(User $user, $includeSubmissionTeam = true)
    {
        $teams = [];
        foreach ($this->projectMembers as $member) {
            if (!$member->getMember()) {
                continue;
            }

            $teamTitle = 'team.undefined';
            if ($member->getMember()->getTeam()) {
                $teamTitle = $member->getMember()->getTeam()->getTitle();
            }

            if ($member->getMember()->getEmployeeStatus() != 'inactive') {
                if (!isset($teams[$teamTitle]) || !in_array($member->getMember(), $teams[$teamTitle])) {
                    $teams[$teamTitle][] = $member->getMember();
                }
            }
        }

        if ($user->getSubmissionTeam() && $includeSubmissionTeam) {
            foreach ($user->getSubmissionTeam()->getTeamMembers() as $member) {
                if (!isset($teams[$user->getSubmissionTeam()->getTitle()]) ||
                    !in_array($member, $teams[$user->getSubmissionTeam()->getTitle()])) {
                    $teams[$user->getSubmissionTeam()->getTitle()][] = $member;
                }
            }
        }

        return $teams;
    }

    /**
     * @return array
     */
    public static function getPriorityChoices()
    {
        return [
            self::PRIORITY_A_PLUS => 'A+',
            self::PRIORITY_A => 'A',
            self::PRIORITY_B => 'B',
            self::PRIORITY_C => 'C'
        ];
    }

    /**
     * @return array
     */
    public function getPriorityLabels()
    {
        return [
            self::PRIORITY_A_PLUS => 'danger',
            self::PRIORITY_A => 'warning',
            self::PRIORITY_B => 'primary',
            self::PRIORITY_C => 'success'
        ];
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isUserPartOfTeam(User $user)
    {
        foreach ($this->getProjectMembers() as $projectMember) {
            if ($projectMember->getMember()->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return bool
     */
    public function canEditProjectMember(User $user)
    {
        return $this->getLeader()->getId() == $user->getId() || $user->canEditProjectTeamMember();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkGrants(User $user)
    {
        return $user->hasFullAccess() ||
            ($this->getLeader() && $this->getLeader()->getId() == $user->getId()) ||
            $this->isUserPartOfTeam($user) ||
            $this->getType() != 'project';
    }

    public function getCategoryPurchases($categoryTitle)
    {
        $categoryPurchases = [];
        foreach ($this->projectPurchases as $projectPurchase) {
            $isCategoryItem = false;
            foreach ($projectPurchase->getItems() as $item) {
                if (!empty($item->getCategory()) and $item->getCategory()->getTitle() == $categoryTitle) {
                    $isCategoryItem = true;
                }
            }
            if ($isCategoryItem) {
                $categoryPurchases[] = $projectPurchase;
            }
        }

        return $categoryPurchases;
    }
}

