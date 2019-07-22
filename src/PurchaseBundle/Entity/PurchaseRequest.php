<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\PurchaseConstants;

/**
 * PurchaseRequest
 *
 * @ORM\Table(name="purchase_request", indexes={@ORM\Index(name="idx_project_leader_id", columns={"project_leader_id"})})
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\PurchaseRequestRepository")
 */
class PurchaseRequest
{
    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 2;
    const PRIORITY_HIGH = 3;
    const PRIORITY_HIGHEST = 4;

    const PAYMENT_ACCOUNT_UFK = 'account_ufk';
    const PAYMENT_ACCOUNT_AT = 'account_at';
    const PAYMENT_ACCOUNT_SPECIAL = 'account_special';

    const ACCEPTANCE_TYPE_WITHOUT = 'acceptance_without';
    const ACCEPTANCE_TYPE_OTK = 'acceptance_otk';
    const ACCEPTANCE_TYPE_5VP = 'acceptance_5vp';

    const EXPENSES_TYPE_NOT_CATEGORIZED = 'expenses.not_categorized';
    const EXPENSES_TYPE_ADDITIONAL = 'expenses.additional';
    const EXPENSES_TYPE_MATERIALS = 'expenses.materials';
    const EXPENSES_TYPE_OTHER_DIRECT = 'expenses.other_direct';

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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="comment_text", type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", name="preferred_shipment_date", nullable=true)
     */
    private $preferredShipmentDate;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\SuppliesCategory")
     * @ORM\JoinColumn(name="supplies_category_id", referencedColumnName="id")
     */
    private $suppliesCategory;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id")
     */
    private $leader;

    /**
     * @var boolean
     * @ORM\Column(name="leader_approved", type="boolean")
     */
    private $leaderApproved = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="project_leader_id", referencedColumnName="id")
     */
    private $projectLeader;

    /**
     * @var boolean
     * @ORM\Column(name="project_leader_approved", type="boolean", nullable=true)
     */
    private $projectLeaderApproved;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="production_leader_id", referencedColumnName="id")
     */
    private $productionLeader;

    /**
     * @var boolean
     * @ORM\Column(name="production_leader_approved", type="boolean")
     */
    private $productionLeaderApproved = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="purchasing_leader_id", referencedColumnName="id")
     */
    private $purchasingLeader;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="financial_leader_id", referencedColumnName="id")
     */
    private $financialLeader;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="financial_manager_id", referencedColumnName="id")
     */
    private $financialManager;

    /**
     * @var boolean
     * @ORM\Column(name="financial_leader_approved", type="boolean")
     */
    private $financialLeaderApproved = false;

    /**
     * @var boolean
     * @ORM\Column(name="purchasing_leader_approved", type="boolean")
     */
    private $purchasingLeaderApproved = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="purchasing_manager_id", referencedColumnName="id")
     */
    private $purchasingManager;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string")
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="type_of_production", type="string", nullable=true)
     */
    private $typeOfProduction;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_status", type="string", nullable=true)
     */
    private $paymentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_status", type="string", nullable=true)
     */
    private $deliveryStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="production_status", type="string", nullable=true)
     */
    private $productionStatus;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="relevance_date", nullable=true)
     */
    private $relevanceDate;

    /**
     * @ORM\Column(type="datetime", name="payment_date", nullable=true)
     */
    private $paymentDate;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="RequestItem", mappedBy="purchaseRequest", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="purchaseRequest", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $invoices;

    /**
     * @ORM\OneToMany(targetEntity="RequestMovement", mappedBy="request", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $movements;

    /**
     * @ORM\ManyToOne(targetEntity="ProductionBundle\Entity\Ware")
     * @ORM\JoinColumn(name="ware_id", referencedColumnName="id")
     */
    private $ware;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\RequestTimings", cascade="all")
     * @ORM\JoinColumn(name="request_timings_id", referencedColumnName="id")
     */
    private $timings;

    /**
     * Request subscribers.
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"all"})
     * @ORM\JoinTable(name="requests_subscribers",
     *      joinColumns={@ORM\JoinColumn(name="request_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $subscribers;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_payment", type="string", length=255, nullable=true)
     */
    private $invoicePayment;

    /**
     * @var string
     *
     * @ORM\Column(name="acceptance_type", type="string", length=255, nullable=true)
     */
    private $acceptanceType;

    /**
     * @var string
     *
     * @ORM\Column(name="expenses_type", type="string", length=255, nullable=true)
     */
    private $expensesType;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_products", type="integer", options={"default":"1"}, nullable=true)
     */
    private $numberOfProducts;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->relevanceDate = new \DateTime();
        $this->status = PurchaseConstants::STATUS_NEW;
        $this->items = new ArrayCollection();
        $this->preferredShipmentDate = new \DateTime();
        $this->timings = new RequestTimings();

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
     * @return mixed
     */
    public function getSuppliesCategory()
    {
        return $this->suppliesCategory;
    }

    /**
     * @param mixed $suppliesCategory
     * @return PurchaseRequest
     */
    public function setSuppliesCategory($suppliesCategory)
    {
        $this->suppliesCategory = $suppliesCategory;
        return $this;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return PurchaseRequest
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        $this->addSubscriber($owner);

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
     * Set status
     *
     * @param string $status
     *
     * @return PurchaseRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return PurchaseRequest
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
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
     * @return PurchaseRequest
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfProduction()
    {
        return $this->typeOfProduction;
    }

    /**
     * @param string $typeOfProduction
     * @return PurchaseRequest
     */
    public function setTypeOfProduction($typeOfProduction)
    {
        $this->typeOfProduction = $typeOfProduction;
        return $this;
    }

    /**
     * Set paymentStatus
     *
     * @param string $paymentStatus
     *
     * @return PurchaseRequest
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set deliveryStatus
     *
     * @param string $deliveryStatus
     *
     * @return PurchaseRequest
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    /**
     * Get deliveryStatus
     *
     * @return string
     */
    public function getDeliveryStatus()
    {
        return $this->deliveryStatus;
    }

    /**
     * @return mixed
     */
    public function getProductionStatus()
    {
        return $this->productionStatus;
    }

    /**
     * @param mixed $productionStatus
     * @return PurchaseRequest
     */
    public function setProductionStatus($productionStatus)
    {
        $this->productionStatus = $productionStatus;
        return $this;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return PurchaseRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime()
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getRelevanceDate()
    {
        return $this->relevanceDate;
    }

    /**
     * @param \DateTime $relevanceDate
     * @return PurchaseRequest
     */
    public function setRelevanceDate($relevanceDate)
    {
        $this->relevanceDate = $relevanceDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param \DateTime $paymentDate
     * @return PurchaseRequest
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * @param Project $project
     *
     * @return PurchaseRequest
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return RequestItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param RequestItem[] $items
     *
     * @return PurchaseRequest
     */
    public function setItems($items)
    {
        $this->items = $items;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @param mixed $invoices
     * @return PurchaseRequest
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;
        return $this;
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
     * @return PurchaseRequest
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return PurchaseRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPreferredShipmentDate()
    {
        return $this->preferredShipmentDate;
    }

    /**
     * @param \DateTime $preferredShipmentDate
     * @return $this
     */
    public function setPreferredShipmentDate($preferredShipmentDate)
    {
        $this->preferredShipmentDate = $preferredShipmentDate;

        return $this;
    }

    /**
     * @return User
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param User $leader
     * @return PurchaseRequest
     */
    public function setLeader($leader)
    {
        $this->leader = $leader;
        $this->addSubscriber($leader);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLeaderApproved()
    {
        return $this->leaderApproved;
    }

    /**
     * @param boolean $leaderApproved
     * @return PurchaseRequest
     */
    public function setLeaderApproved($leaderApproved)
    {
        $this->leaderApproved = $leaderApproved;
        $this->getTimings()->setLeaderApprovedAt(new \DateTime());

        return $this;
    }

    /**
     * @return User
     */
    public function getProjectLeader()
    {
        return $this->projectLeader;
    }

    /**
     * @param User $projectLeader
     * @return PurchaseRequest
     */
    public function setProjectLeader($projectLeader)
    {
        $this->projectLeader = $projectLeader;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isProjectLeaderApproved()
    {
        return $this->projectLeaderApproved;
    }

    /**
     * @param boolean $projectLeaderApproved
     * @return PurchaseRequest
     */
    public function setProjectLeaderApproved($projectLeaderApproved)
    {
        $this->projectLeaderApproved = $projectLeaderApproved;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductionLeader()
    {
        return $this->productionLeader;
    }

    /**
     * @param mixed $productionLeader
     * @return PurchaseRequest
     */
    public function setProductionLeader($productionLeader)
    {
        $this->productionLeader = $productionLeader;
        $this->addSubscriber($productionLeader);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isProductionLeaderApproved()
    {
        return $this->productionLeaderApproved;
    }

    /**
     * @param boolean $productionLeaderApproved
     * @return PurchaseRequest
     */
    public function setProductionLeaderApproved($productionLeaderApproved)
    {
        $this->productionLeaderApproved = $productionLeaderApproved;
        $this->getTimings()->setProductionLeaderApprovedAt(new \DateTime());
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinancialLeader()
    {
        return $this->financialLeader;
    }

    /**
     * @param mixed $financialLeader
     * @return PurchaseRequest
     */
    public function setFinancialLeader($financialLeader)
    {
        $this->financialLeader = $financialLeader;
        $this->addSubscriber($financialLeader);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinancialManager()
    {
        return $this->financialManager;
    }

    /**
     * @param mixed $financialManager
     * @return PurchaseRequest
     */
    public function setFinancialManager($financialManager)
    {
        $this->financialManager = $financialManager;
        $this->addSubscriber($financialManager);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFinancialLeaderApproved()
    {
        return $this->financialLeaderApproved;
    }

    /**
     * @param boolean $financialLeaderApproved
     * @return PurchaseRequest
     */
    public function setFinancialLeaderApproved($financialLeaderApproved)
    {
        $this->financialLeaderApproved = $financialLeaderApproved;
        $this->getTimings()->setFinancialLeaderApprovedAt(new \DateTime());

        return $this;
    }

    /**
     * @return User
     */
    public function getPurchasingLeader()
    {
        return $this->purchasingLeader;
    }

    /**
     * @param User $purchasingLeader
     * @return PurchaseRequest
     */
    public function setPurchasingLeader($purchasingLeader)
    {
        $this->purchasingLeader = $purchasingLeader;
        $this->addSubscriber($purchasingLeader);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPurchasingLeaderApproved()
    {
        return $this->purchasingLeaderApproved;
    }

    /**
     * @param boolean $purchasingLeaderApproved
     * @return PurchaseRequest
     */
    public function setPurchasingLeaderApproved($purchasingLeaderApproved)
    {
        $this->purchasingLeaderApproved = $purchasingLeaderApproved;
        $this->getTimings()->setPurchasingLeaderApprovedAt(new \DateTime());

        return $this;
    }

    /**
     * @return User
     */
    public function getPurchasingManager()
    {
        return $this->purchasingManager;
    }

    /**
     * @param User $purchasingManager
     * @return PurchaseRequest
     */
    public function setPurchasingManager($purchasingManager)
    {
        $this->purchasingManager = $purchasingManager;
        $this->addSubscriber($purchasingManager);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovements()
    {
        return $this->movements;
    }

    /**
     * @param mixed $movements
     * @return PurchaseRequest
     */
    public function setMovements($movements)
    {
        $this->movements = $movements;
        return $this;
    }

    /**
     * Set ware
     *
     * @param Ware $ware
     *
     * @return $this
     */
    public function setWare($ware)
    {
        $this->ware = $ware;

        return $this;
    }

    /**
     * @return Ware
     */
    public function getWare()
    {
        return $this->ware;
    }

    /**
     * @return RequestTimings
     */
    public function getTimings()
    {
        return $this->timings;
    }

    /**
     * @param mixed $timings
     * @return PurchaseRequest
     */
    public function setTimings($timings)
    {
        $this->timings = $timings;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoicePayment()
    {
        return $this->invoicePayment;
    }

    /**
     * @param string $invoicePayment
     * @return PurchaseRequest
     */
    public function setInvoicePayment($invoicePayment)
    {
        $this->invoicePayment = $invoicePayment;
        return $this;
    }

    /**
     * @return string
     */
    public function getAcceptanceType()
    {
        return $this->acceptanceType;
    }

    /**
     * @param string $acceptanceType
     * @return PurchaseRequest
     */
    public function setAcceptanceType($acceptanceType)
    {
        $this->acceptanceType = $acceptanceType;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpensesType()
    {
        return $this->expensesType;
    }

    /**
     * @param string $expensesType
     * @return PurchaseRequest
     */
    public function setExpensesType($expensesType)
    {
        $this->expensesType = $expensesType;
        return $this;
    }

    /**
     * Set numberOfProducts
     *
     * @param int $numberOfProducts
     *
     * @return PurchaseRequest
     */
    public function setNumberOfProducts($numberOfProducts)
    {
        $this->numberOfProducts = $numberOfProducts;

        return $this;
    }

    /**
     * Get numberOfProducts
     *
     * @return int
     */
    public function getNumberOfProducts()
    {
        return $this->numberOfProducts;
    }

    /**
     * @return ArrayCollection
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
        if ($subscriber instanceof User && !$this->subscribers->contains($subscriber)) {
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

    /**
     * @param User $currentUser
     * @return array
     */
    public function getRequestRecipients(User $currentUser)
    {
        $recipients = [];

        foreach ($this->getSubscribers() as $subscriber) {
            if ($subscriber->getId() == $currentUser->getId()) {
                continue;
            }

            $recipients[] = $subscriber;
        }

        return $recipients;
    }

    /**
     * @return array
     */
    protected function getStatesTransitions()
    {
        return [
            PurchaseConstants::STATUS_NEW => [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                PurchaseConstants::STATUS_REJECTED
            ],
            PurchaseConstants::STATUS_NEEDS_FIXING => [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                PurchaseConstants::STATUS_REJECTED
            ],
            PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL => [
                $this->getStateTransitionsByType( PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL),
                PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE,
                PurchaseConstants::STATUS_NEEDS_FIXING
            ],
            PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE => [
                PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_FIXING,
            ],
            PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL => [
                PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_REJECTED,
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK,
                PurchaseConstants::STATUS_MANAGER_ASSIGNED
            ],
            PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER => [
                PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_REJECTED,
            ],
            PurchaseConstants::STATUS_MANAGER_ASSIGNED => [
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE
            ],
            PurchaseConstants::STATUS_REJECTED => [PurchaseConstants::STATUS_NEEDS_FIXING],
            PurchaseConstants::STATUS_MANAGER_STARTED_WORK => [
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
            ],
            PurchaseConstants::STATUS_MANAGER_FINISHED_WORK => [
                PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                PurchaseConstants::STATUS_DONE,
                PurchaseConstants::STATUS_REJECTED
            ],
            PurchaseConstants::STATUS_DONE => [],
            PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE => [
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE,
                PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE
            ],
            PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE => [
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE
            ]
        ];
    }

    protected function getStateTransitionsByType($state)
    {
        $typeTransitions = [
            PurchaseConstants::TYPE_PURCHASE => [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL => [
                    PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                    PurchaseConstants::STATUS_NEEDS_FIXING,
                    PurchaseConstants::STATUS_REJECTED,
                ]
            ],
            PurchaseConstants::TYPE_PRODUCTION => [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL => [
                    PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                    PurchaseConstants::STATUS_NEEDS_FIXING,
                    PurchaseConstants::STATUS_REJECTED,
                ]
            ],
            PurchaseConstants::TYPE_MOVEMENT => [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL => [
                    PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                    PurchaseConstants::STATUS_NEEDS_FIXING,
                    PurchaseConstants::STATUS_REJECTED,
                    PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE
                ]
            ],
        ];

        return isset($typeTransitions[$this->getType()][$state]) ? $typeTransitions[$this->getType()][$state] : [];
    }

    /**
     * @param $fromState
     * @param $toState
     * @return bool
     */
    public function canTransition($fromState, $toState)
    {
        return ($fromState != $toState) && in_array(
            $toState,
            $this->getStatesTransitions()[$fromState]
        );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canReturnFixing(User $user)
    {
        $preparingTeamReturn = ($this->isOwner($user) ||
                $this->isApprovingLeader($user) ||
                $this->isApprovingProjectLeader($user) ||
                $this->isOwnerTeamLeader($user) ||
                $this->isApprovingProductionLeader($user)) &&
            in_array($this->getStatus(), [
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE,
                PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                PurchaseConstants::STATUS_REJECTED,
                PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE
            ]);

        $purchasingTeamReturn = ($this->isPurchasingLeader($user) || $this->isPurchasingManager($user)) &&
            in_array($this->getStatus(), [
                PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE
            ]);

        return ($preparingTeamReturn || $purchasingTeamReturn) &&
        $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_FIXING)
            ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canReject(User $user)
    {
        $preparingTeamReject = $this->isOwner($user) &&
            in_array($this->getStatus(), [
                PurchaseConstants::STATUS_NEW,
                PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE,
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_FIXING,
            ]);

        return ($preparingTeamReject || $user->hasFullRequestPrivileges()) &&
                $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_REJECTED)
            ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canLeaderApprove(User $user)
    {
        return $this->isApprovingLeader($user) &&
            ($this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER) ||
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL) ||
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE))
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canApprovePreliminaryEstimate(User $user)
    {
        return $this->isApprovingProjectLeader($user) && $this->getStatus() == PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canProjectLeaderApprove(User $user)
    {
        return $this->isApprovingProjectLeader($user) &&
            ($this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER) ||
                $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL))
            ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canProductionLeaderApprove(User $user)
    {
        return $this->isApprovingProductionLeader($user) &&
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER)
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canProductionLeaderMarkAsProduced(User $user)
    {
        $hasItemsInProduction = false;

        foreach ($this->getItems() as $item) {
            if ($item->getProductionStatus() == RequestItem::PRODUCTION_STATUS_IN_PRODUCTION) {
                $hasItemsInProduction = true;
            }
        }

        return $this->isApprovingProductionLeader($user) &&
            $this->getProductionStatus() == PurchaseConstants::PRODUCTION_STATUS_IN_PRODUCTION &&
            !$hasItemsInProduction
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canProductionLeaderMarkItemAsProduced(User $user)
    {
        return $this->isApprovingProductionLeader($user) &&
            $this->getProductionStatus() == PurchaseConstants::PRODUCTION_STATUS_IN_PRODUCTION
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canMarkItemsOnStock(User $user)
    {
        return $user->canMarkOnStock() &&
            in_array(
                $this->status,
                [PurchaseConstants::STATUS_MANAGER_STARTED_WORK, PurchaseConstants::STATUS_MANAGER_FINISHED_WORK]
            )
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canPurchasingLeaderApprove(User $user)
    {
        return $this->isPurchasingLeader($user) &&
            in_array(
                $this->status,
                [PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER, PurchaseConstants::STATUS_MANAGER_ASSIGNED]
            )
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRequestApprove(User $user)
    {
        return $this->isOwner($user) &&
            count($this->getItems()) &&
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL)
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canEditItems(User $user)
    {
        return $this->isOwner($user) &&
            in_array($this->status, [PurchaseConstants::STATUS_NEW, PurchaseConstants::STATUS_NEEDS_FIXING])
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canStartProgress(User $user)
    {
        return $this->isPurchasingManager($user) &&
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE) &&
            !in_array($this->status, [PurchaseConstants::STATUS_MANAGER_FINISHED_WORK, PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE])
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function needApprovePreliminaryEstimate(User $user)
    {
        return $this->isPurchasingManager($user) && $this->getStatus() == PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canManagerFinishWork(User $user)
    {
        return ($this->isPurchasingManager($user) || $user->canChangePaymentStatusPurchase()) &&
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_MANAGER_FINISHED_WORK)
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canManagerPreliminaryEstimate(User $user)
    {
        return ($this->isPurchasingManager($user) || $user->canChangePaymentStatusPurchase()) &&
            $this->canTransition($this->getStatus(), PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE)
            ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canAttachInvoice(User $user)
    {
        return ($this->isPurchasingLeader($user) || $this->isPurchasingManager($user) || $user->canChangePaymentStatusPurchase()) &&
            (
                in_array($this->status, [PurchaseConstants::STATUS_MANAGER_STARTED_WORK ]) ||
                in_array($this->paymentStatus, [PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT])
            )
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canAttachPaymentDocuments(User $user)
    {
        return ($this->isFinancialLeader($user) || $this->isFinancialManager($user) || $user->canChangePaymentStatusPurchase()) &&
            in_array($this->paymentStatus, [
                PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING
            ])
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canExportItems(User $user)
    {
        return $this->isPurchasingLeader($user) ||
            $this->isPurchasingManager($user) ||
            $this->isOwner($user) ||
            $this->isApprovingLeader($user)
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canStartDelivery(User $user)
    {
        return ($this->isPurchasingManager($user) || $this->isPurchasingLeader($user) || $user->canChangePaymentStatusPurchase()) &&
            in_array($this->status, [PurchaseConstants::STATUS_MANAGER_FINISHED_WORK]) &&
            in_array($this->deliveryStatus, [PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canAddDeliveryItem(User $user)
    {
        return $this->isPurchasingManager($user) || $this->isPurchasingLeader($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canFinishDelivery(User $user)
    {
        return ($this->isPurchasingManager($user) || $this->isPurchasingLeader($user) || $user->canChangePaymentStatusPurchase()) &&
            in_array($this->status, [PurchaseConstants::STATUS_MANAGER_FINISHED_WORK]) &&
            in_array($this->deliveryStatus, [PurchaseConstants::DELIVERY_STATUS_IN_DELIVERY]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canMoveToPayment(User $user)
    {
        return ($this->isFinancialLeader($user) || $user->canChangePaymentStatusPurchase() || $user->canChangePaymentStatusPurchase()) &&
            in_array($this->status, [PurchaseConstants::STATUS_MANAGER_FINISHED_WORK]) &&
            in_array($this->paymentStatus, [PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canMarkAsPaid(User $user)
    {
        return ($this->isFinancialLeader($user) || $this->isFinancialManager($user) || $user->canChangePaymentStatusPurchase()) &&
            in_array($this->status, [PurchaseConstants::STATUS_MANAGER_FINISHED_WORK]) &&
            in_array($this->paymentStatus, [PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING]);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canChangeRequestOwner(User $user)
    {
        $team = $this->getOwner()->getTeam();

        return $user->canChangeRequestOwner() || $team->isTeamLeader($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->getOwner()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isApprovingLeader(User $user)
    {
        return $this->getLeader() && $this->getLeader()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isApprovingProjectLeader(User $user)
    {
        return $this->getProjectLeader() && $this->getProjectLeader()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isApprovingProductionLeader(User $user)
    {
        return $this->getProductionLeader() && $this->getProductionLeader()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isOwnerTeamLeader(User $user)
    {
        return $this->getOwner()->getTeam() && $this->getOwner()->getTeam()->getLeader()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isPurchasingLeader(User $user)
    {
        return $this->getPurchasingLeader() && $this->getPurchasingLeader()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isPurchasingManager(User $user)
    {
        return $this->getPurchasingManager() && $this->getPurchasingManager()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isFinancialLeader(User $user)
    {
        return $this->getFinancialLeader() && $this->getFinancialLeader()->getId() == $user->getId();
    }
    /**
     * @param User $user
     * @return bool
     */
    protected function isFinancialManager(User $user)
    {
        return $this->getFinancialManager() && $this->getFinancialManager()->getId() == $user->getId();
    }

    /**
     * Set warehouse
     *
     * @param Warehouse $warehouse
     *
     * @return PurchaseRequest
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
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
            self::PRIORITY_HIGHEST => 'highest',
        ];
    }
    
    /**
     * @return array
     */
    public function getPriorityLabels()
    {
        return [
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_NORMAL => 'primary',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_HIGHEST => 'danger',
        ];
    }

    /**
     * @return array
     */
    public static function getPriorityTitles()
    {
        return [
            self::PRIORITY_LOW => 'C',
            self::PRIORITY_NORMAL => 'B',
            self::PRIORITY_HIGH => 'A',
            self::PRIORITY_HIGHEST => 'A+',
        ];
    }

    /**
     * @return array
     */
    public function getInvoicesList()
    {
        $invoices = [];

        foreach ($this->getItems() as $item) {
            $invoiceNr = $item->getInvoiceNumber() ?: 'Счет не указан';
            if ($item->getPrice()) {
                $currentCost = isset($invoices[$invoiceNr]['cost']) ?
                    $invoices[$invoiceNr]['cost'] : 0;

                $invoices[$invoiceNr] = [
                    'supplier' => $item->getSupplier(),
                    'cost' => (float) ($currentCost + $item->getPrice())
                ];
            }
        }

        return $invoices;
    }

    /**
     * @return array
     */
    public static function getPaymentList()
    {
        return [
            self::PAYMENT_ACCOUNT_UFK => self::PAYMENT_ACCOUNT_UFK,
            self::PAYMENT_ACCOUNT_AT => self::PAYMENT_ACCOUNT_AT,
            self::PAYMENT_ACCOUNT_SPECIAL => self::PAYMENT_ACCOUNT_SPECIAL,
        ];
    }

    /**
     * @return array
     */
    public static function getAcceptanceList()
    {
        return [
            self::ACCEPTANCE_TYPE_WITHOUT => self::ACCEPTANCE_TYPE_WITHOUT,
            self::ACCEPTANCE_TYPE_OTK => self::ACCEPTANCE_TYPE_OTK,
            self::ACCEPTANCE_TYPE_5VP => self::ACCEPTANCE_TYPE_5VP,
        ];
    }

    /**
     * @return array
     */
    public static function getExpensesList()
    {
        return [
            self::EXPENSES_TYPE_NOT_CATEGORIZED => self::EXPENSES_TYPE_NOT_CATEGORIZED,
            self::EXPENSES_TYPE_ADDITIONAL => self::EXPENSES_TYPE_ADDITIONAL,
            self::EXPENSES_TYPE_MATERIALS => self::EXPENSES_TYPE_MATERIALS,
            self::EXPENSES_TYPE_OTHER_DIRECT => self::EXPENSES_TYPE_OTHER_DIRECT
        ];
    }
}

