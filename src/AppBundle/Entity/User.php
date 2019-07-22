<?php

namespace AppBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fos_user_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    const EMPLOYEE_STATUS_ACTIVE = 'active';
    const EMPLOYEE_STATUS_OUTSOURCE = 'outsource';
    const EMPLOYEE_STATUS_INACTIVE = 'inactive';
    const EMPLOYEE_STATUS_HIDDEN = 'hidden';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_chat_id", type="integer", nullable=true)
     */
    protected $telegramChatId;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_username", type="text", nullable=true)
     */
    protected $telegramUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="badge_color", type="text", nullable=true)
     */
    protected $badgeColor;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectRole")
     * @ORM\JoinColumn(name="employee_role_id", referencedColumnName="id")
     */
    private $employeeRole;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="text", nullable=true)
     */
    protected $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="text", nullable=true)
     */
    protected $theme = 'default';

    /**
     * @var string
     *
     * @ORM\Column(name="show_menu", type="text", nullable=true)
     */
    protected $showMenu = 'on';

    /**
     * @var string
     *
     * @ORM\Column(name="employee_status", type="text", nullable=true, options={"default" : "active"})
     */
    protected $employeeStatus = 'active';

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     * @ORM\JoinColumn(name="submission_team", referencedColumnName="id")
     */
    protected $submissionTeam;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="text", nullable=true)
     */
    protected $room = '';

    /**
     * @ORM\Column(type="datetime", name="employment_date", nullable=true)
     */
    protected $employmentDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="admin", type="boolean", options={"default": "0"})
     */
    protected $admin = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="half_time", type="boolean")
     */
    protected $halfTime = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_online", type="datetime", nullable=true)
     */
    private $lastOnline;

    /**
     * @var boolean
     *
     * @ORM\Column(name="close_own_tasks", type="boolean", options={"default": "0"})
     */
    protected $closeOwnTasks = false;

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
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
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getBadgeColor()
    {
        return $this->badgeColor;
    }

    /**
     * @param string $badgeColor
     * @return User
     */
    public function setBadgeColor($badgeColor)
    {
        $this->badgeColor = $badgeColor;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTelegramUsername()
    {
        return $this->telegramUsername;
    }

    /**
     * @param string $telegramUsername
     */
    public function setTelegramUsername($telegramUsername)
    {
        $this->telegramUsername = $telegramUsername;
    }

    /**
     * @return mixed
     */
    public function getEmployeeRole()
    {
        return $this->employeeRole;
    }

    /**
     * @param ProjectRole $employeeRole
     *
     * @return $this
     */
    public function setEmployeeRole($employeeRole)
    {
        $this->employeeRole = $employeeRole;

        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     * @return User
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * @param string $middlename
     *
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     * @return User
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @return string
     */
    public function getShowMenu()
    {
        return $this->showMenu;
    }

    /**
     * @param string $showMenu
     * @return User
     */
    public function setShowMenu($showMenu)
    {
        $this->showMenu = $showMenu;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
    }

    /**
     * @param string $employeeStatus
     * @return User
     */
    public function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;
        return $this;
    }

    /**
     * @return Team
     */
    public function getSubmissionTeam()
    {
        return $this->submissionTeam;
    }

    /**
     * @param mixed $submissionTeam
     * @return User
     */
    public function setSubmissionTeam($submissionTeam)
    {
        $this->submissionTeam = $submissionTeam;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     * @return User
     */
    public function setRoom($room)
    {
        $this->room = $room;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmploymentDate()
    {
        return $this->employmentDate;
    }

    /**
     * @param mixed $employmentDate
     * @return User
     */
    public function setEmploymentDate($employmentDate)
    {
        $this->employmentDate = $employmentDate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param boolean $admin
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCloseOwnTasks()
    {
        return $this->closeOwnTasks;
    }

    /**
     * @param boolean $closeOwnTasks
     * @return User
     */
    public function setCloseOwnTasks($closeOwnTasks)
    {
        $this->closeOwnTasks = $closeOwnTasks;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHalfTime()
    {
        return $this->halfTime;
    }

    /**
     * @param boolean $halfTime
     * @return User
     */
    public function setHalfTime($halfTime)
    {
        $this->halfTime = $halfTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastOnline()
    {
        return $this->lastOnline;
    }

    /**
     * @param \DateTime $lastOnline
     * @return User
     */
    public function setLastOnline($lastOnline)
    {
        $this->lastOnline = $lastOnline;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullname($withMiddle = false)
    {
        if ($withMiddle) {
            return sprintf('%s %s %s', $this->getFirstname(), $this->getMiddlename(), $this->getLastname());
        }

        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    /**
     * {@inheritdoc}
     */
    public function getLastNameWithInitials()
    {
        $firstName = mb_substr($this->getFirstname(), 0, 1) ? mb_substr($this->getFirstname(), 0, 1) . '.' : '';
        $middleName = mb_substr($this->getMiddlename(), 0, 1) ? mb_substr($this->getMiddlename(), 0, 1) . '.' : '';
        return sprintf('%s %s%s', $this->getLastname(), $firstName, $middleName);
    }

    /**
     * @return bool
     */
    public function isProjectOfficeLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PROJECT_OFFICE_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isTechnicalLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_TECHNICAL_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isITRequestManager()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_IT_REQUEST_MANAGER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPurchasingManager()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PURCHASING_MANAGER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPurchasingLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PURCHASING_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isProjectLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PROJECT_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPurchaseRequestApprovingLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PURCHASE_REQUEST_APPROVING_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isProductionLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PURCHASE_REQUEST_PRODUCTION_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isFinancialManager()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_FINANCIAL_MANAGER')) {
                return true;
            }
        }

        return false;
    }
    /**
     * @return bool
     */
    public function isFinancialLeader()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_FINANCIAL_LEADER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditProjectTeamMember()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_PROJECT_ADD_TEAM_MEMBER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canDeleteProjectFiles()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_DELETE_PROJECT_FILES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewFinancialInfo()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_VIEW_FINANCIAL_INFO')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasAccessToProjectCosts()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_ACCESS_TO_PROJECT_COSTS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasFullRequestPrivileges()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_HAS_FULL_REQUEST_PRIVILEGES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canMarkOnStock()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_MARK_ON_STOCK')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewSupplier()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_SUPPLIER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasAccessToBlackList()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_ACCESS_TO_BLACKLIST')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewToolWorkLog()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_TOOL_WORK_LOG')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewClient()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_CLIENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditClient()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_CLIENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewSerialProduction()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_SERIAL_PRODUCTION')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewRent()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_RENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canAddRent()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_ADD_RENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canImportRent()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_IMPORT_RENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewTenement()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_TENEMENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditTenement()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_TENEMENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditSerialProduction()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_SERIAL_PRODUCTION')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewAllDocuments()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ALL_DOCUMENTS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canCancelAllTasks()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_CANCEL_ALL_TASKS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canAddUserAchievements()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_ADD_USER_ACHIEVEMENTS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canApproveResult()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_APPROVE_RESULT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewAllProtocols()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ALL_PROTOCOLS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewAllInventoryItems()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ALL_INVENTORY_ITEMS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditEngineeringDocument()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_ENGINEERING_DOCUMENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditProgrammingDocument()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_PROGRAMMING_DOCUMENT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditSupplier()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_SUPPLIER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewProjectCode()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_PROJECT_CODE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditProjectCode()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_PROJECT_CODE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canDeleteProjectCode()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_DELETE_PROJECT_CODE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewActivity()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ACTIVITY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewAllActivity()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ALL_ACTIVITY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canDeleteSupplierPerson()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_DELETE_SUPPLIER_PERSON')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canExportVacationCalendar()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EXPORT_VACATION_CALENDAR')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewInfrastructure()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_INFRASTRUCTURE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canDownloadOverdueInvoices()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_DOWNLOAD_OVERDUE_INVOICES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canChangePaymentStatusPurchase()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_CHANGE_PAYMENT_STATUS_PURCHASE')) {
                return true;
            }
        }

        return false;
    }

    public function canEditEmployees()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_EMPLOYEES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canProcessProductionPurchases()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_PROCESS_PRODUCTION_PURCHASES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewNomenclature()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_NOMENCLATURE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditNomenclature()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_NOMENCLATURE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasFullAccess()
    {
        return $this->isProjectOfficeLeader() ||
            $this->isTechnicalLeader();
    }

    /**
     * @param string $role
     * @return bool
     */
    public function isGranted($role)
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @return array
     */
    public static function getEmployeeStatuses()
    {
        return [
            self::EMPLOYEE_STATUS_ACTIVE => 'Active Employee',
            self::EMPLOYEE_STATUS_OUTSOURCE => 'Outsource Employee',
            self::EMPLOYEE_STATUS_INACTIVE => 'Inactive Employee'
        ];
    }

    /**
     * @return array
     */
    public static function getEmployeeStatusChoices()
    {
        return array_flip(self::getEmployeeStatuses());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFullname();
    }

    /**
     * @return bool
     */
    public function canViewApplicant()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_APPLICANT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditApplicant()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_APPLICANT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewVacancy()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_VACANCY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditVacancy()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_VACANCY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewInterview()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_INTERVIEW')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditInterview()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_INTERVIEW')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditLibrary()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_LIBRARY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewEngineeringDocumentClassifier()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_ENGINEERING_DOCUMENT_CLASSIFIER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditEngineeringDocumentClassifier()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_ENGINEERING_DOCUMENT_CLASSIFIER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canExportJobReport()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EXPORT_JOB_REPORT')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canChangeRequestOwner()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_CHANGE_REQUEST_OWNER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewRegistry()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_REGISTRY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditRegistry()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_REGISTRY')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canViewMedicalInstitutions()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_MEDICAL_INSTITUTIONS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditMedicalInstitutions()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_Edit_MEDICAL_INSTITUTIONS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasAccessFiles()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_HAS_ACCESS_FILES')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canRegisterDocuments()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_REGISTER_DOCUMENTS')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canSendPurchaseToSupplyArea()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_SEND_PURCHASE_TO_SUPPLY_AREA')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ProjectTask $task
     * @return bool
     */
    public function canCloseOwnTasks(ProjectTask $task)
    {
        return $this->getCloseOwnTasks() &&
            $this->getId() == $task->getReporter()->getId() &&
            $this->getId() == $task->getControllingUser()->getId();
    }

    /**
     * @return bool
     */
    public function canViewProjectPrice()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_VIEW_PROJECT_PRICE')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canEditProjectPrice()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_CAN_EDIT_PROJECT_PRICE')) {
                return true;
            }
        }

        return false;
    }
}
