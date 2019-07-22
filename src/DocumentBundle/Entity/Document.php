<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\Supplier;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\DocumentRepository")
 */
class Document
{
    const BLANK_DIGITAL = 'digital';
    const BLANK_ANALOG = 'analog';

    const DOCUMENT_STATUS_NEW = 1;
    const DOCUMENT_STATUS_ON_HOLD = 2;
    const DOCUMENT_STATUS_NEEDS_FIXING = 3;
    const DOCUMENT_STATUS_NEEDS_APPROVE = 4;
    const DOCUMENT_STATUS_APPROVED = 5;
    const DOCUMENT_STATUS_REGISTERED = 6;
    const DOCUMENT_STATUS_CANCELLED = 7;

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
    private $status = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="unlimited", type="boolean")
     */
    private $unlimited = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contract_extension", type="boolean")
     */
    private $contractExtension = false;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\DocumentTemplate")
     * @ORM\JoinColumn(name="document_template_id", referencedColumnName="id")
     */
    private $documentTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\DocumentTemplate")
     * @ORM\JoinColumn(name="document_template_supplementary_id", referencedColumnName="id", nullable=true)
     */
    private $documentTemplateSupplementary;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\DocumentCategory")
     * @ORM\JoinColumn(name="document_category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="period", type="integer", nullable=true)
     */
    private $period;

    /**
     * @var float
     *
     * @ORM\Column(name="sum_document", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable=true)
     */
    private $vat;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @ORM\OneToMany(targetEntity="DocumentRevision", mappedBy="document", cascade="all")
     * @ORM\OrderBy({"version" = "DESC"})
     */
    private $revisions;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentRevision", cascade="persist")
     * @ORM\JoinColumn(name="last_revision_id", referencedColumnName="id")
     */
    private $lastRevision;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"all"})
     * @ORM\JoinTable(name="documents_subscribers",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $subscribers;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="curator_id", referencedColumnName="id")
     */
    private $curator;

    /**
     * @var string
     *
     * @ORM\Column(name="document_subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_contract_code", type="string", length=255, nullable=true)
     */
    private $supplierContractCode;

    /**
     * @var string
     *
     * @ORM\Column(name="measure_of_responsibility", type="text")
     */
    private $measureOfResponsibility;

    /**
     * @var string
     *
     * @ORM\Column(name="security", type="string", length=255, nullable=true)
     */
    private $security;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\Document")
     * @ORM\JoinColumn(name="parent_document", referencedColumnName="id", nullable=true)
     */
    private $parentDocument;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debt_receivable", type="datetime", nullable=true)
     */
    private $debtReceivable;

    /**
     * @var string
     *
     * @ORM\Column(name="act", type="string", length=255, nullable=true)
     */
    private $act;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="DocumentSignatory", mappedBy="document", cascade="all")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $signatories;

    /**
     * @var string
     *
     * @ORM\Column(name="one_s_unique_code", type="string", length=255, nullable=true, unique=true)
     */
    private $oneSUniqueCode;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->subscribers = new ArrayCollection();
        $this->type = self::BLANK_ANALOG;
        $this->status = self::DOCUMENT_STATUS_NEW;
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
     * Regenerate code
     *
     * @param string $code
     *
     * @return Document
     */
    public function regenerateCode($team, $project, $countDocumentTemplateSupplementary)
    {
        $code = $this->getId() . '.' .
            'AT' . '.' .
            date('y') . '.' .
            $this->getDocumentTemplate()->getCode() . '.' .
            $team->getCode();

        if ($this->getProject()->getCode() != $team->getCode()) {
            $code = $code . '.' . $project->getCode();
        }

        if (!empty($countDocumentTemplateSupplementary)) {
            $code = $code . '.' . $this->getDocumentTemplateSupplementary()->getCode() . $countDocumentTemplateSupplementary;
        }

        if ($code != $this->getCode()) {
            $this->code = $code;
        }

        return $this;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Document
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnLimited()
    {
        return $this->unlimited;
    }

    /**
     * @param boolean $unlimited
     * @return Document
     */
    public function setUnLimited($unlimited)
    {
        $this->unlimited = $unlimited;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isContractExtension()
    {
        return $this->contractExtension;
    }

    /**
     * @param boolean $contractExtension
     * @return Document
     */
    public function setContractExtension($contractExtension)
    {
        $this->contractExtension = $contractExtension;
        return $this;
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
     * Set supplier
     *
     * @param Supplier $supplier
     *
     * @return $this
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @return mixed
     */
    public function getDocumentTemplate()
    {
        return $this->documentTemplate;
    }

    /**
     * @param DocumentTemplate $documentTemplate
     * @return Document
     */
    public function setDocumentTemplate($documentTemplate)
    {
        $this->documentTemplate = $documentTemplate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDocumentTemplateSupplementary()
    {
        return $this->documentTemplateSupplementary;
    }

    /**
     * @param DocumentTemplate $documentTemplateSupplementary
     * @return Document
     */
    public function setDocumentTemplateSupplementary($documentTemplateSupplementary)
    {
        $this->documentTemplateSupplementary = $documentTemplateSupplementary;
        return $this;
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
     * @return Document
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     * @return Document
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
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
     * @return Document
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * Set amount
     * @param float $amount
     * @return Document
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set vat
     * @param float $vat
     * @return Document
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
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
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * @param mixed $revisions
     * @return Document
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastRevision()
    {
        return $this->lastRevision;
    }

    /**
     * @param mixed $lastRevision
     * @return Document
     */
    public function setLastRevision($lastRevision)
    {
        $this->lastRevision = $lastRevision;
        return $this;
    }

    /**
     * Set curator
     *
     * @param User $curator
     *
     * @return $this
     */
    public function setCurator($curator)
    {
        $this->curator = $curator;

        return $this;
    }

    /**
     * Get curator
     *
     * @return User
     */
    public function getCurator()
    {
        return $this->curator;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Document
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Set period
     *
     * @param int $period
     *
     * @return Document
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     * @param string $subject
     * @return Document
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get supplierContractCode
     *
     * @return string
     */
    public function getSupplierContractCode()
    {
        return $this->supplierContractCode;
    }

    /**
     * Set supplierContractCode
     * @param string $supplierContractCode
     * @return Document
     */
    public function setSupplierContractCode($supplierContractCode)
    {
        $this->supplierContractCode = $supplierContractCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMeasureOfResponsibility()
    {
        return $this->measureOfResponsibility;
    }

    /**
     * @param string $measureOfResponsibility
     * @return Document
     */
    public function setMeasureOfResponsibility($measureOfResponsibility)
    {
        $this->measureOfResponsibility = $measureOfResponsibility;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @param string $security
     * @return Document
     */
    public function setSecurity($security)
    {
        $this->security = $security;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentDocument()
    {
        return $this->parentDocument;
    }

    /**
     * @param Document $parentDocument
     * @return Document
     */
    public function setParentDocument($parentDocument)
    {
        $this->parentDocument = $parentDocument;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDebtReceivable()
    {
        return $this->debtReceivable;
    }

    /**
     * @param \DateTime $debtReceivable
     * @return Document
     */
    public function setDebtReceivable($debtReceivable)
    {
        $this->debtReceivable = $debtReceivable;
        return $this;
    }

    /**
     * @return string
     */
    public function getAct()
    {
        return $this->act;
    }

    /**
     * @param string $act
     * @return Document
     */
    public function setAct($act)
    {
        $this->act = $act;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Document
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Set oneSUniqueCode
     *
     * @param string $oneSUniqueCode
     *
     * @return Document
     */
    public function setOneSUniqueCode($oneSUniqueCode)
    {
        $this->oneSUniqueCode = $oneSUniqueCode;

        return $this;
    }

    /**
     * Get oneSUniqueCode
     *
     * @return string
     */
    public function getOneSUniqueCode()
    {
        return $this->oneSUniqueCode;
    }

    /**
     * @return DocumentSignatory[]
     */
    public function getSignatories()
    {
        return $this->signatories;
    }

    /**
     * @param DocumentSignatory $signatories
     * @return Document
     */
    public function setSignatories($signatories)
    {
        $this->signatories = $signatories;
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
     * @param User $user
     * @return bool
     */
    public function canRequestApprove(User $user)
    {
        return $this->isOwner($user) &&
            (
                $this->isNew() ||
                $this->isNeedsFixing()
            )
        ;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canEditDocument(User $user)
    {
        return $this->isOwner($user) &&
            (
                $this->isCancelled() ||
                $this->isNew() ||
                $this->isNeedsFixing() ||
                $this->isNeedsApprove() ||
                $this->isRegistered() ||
                $this->isOnHold()
            );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canReturnFixing(User $user)
    {
        return ($this->isOwner($user) || $this->getSignatory($user)) && ($this->isNeedsApprove() || $this->isCancelled()) &&
            !$this->isNeedsFixing();
    }

    /**
     * @return bool
     */
    public function canCancelled()
    {
        if (!$this->isCancelled()) {
            return true;
        }
        return false;
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
     * @return DocumentSignatory|bool
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
    public function isNew()
    {
        return $this->status == self::DOCUMENT_STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isNeedsFixing()
    {
        return $this->status == self::DOCUMENT_STATUS_NEEDS_FIXING;
    }

    /**
     * @return bool
     */
    public function isNeedsApprove()
    {
        return $this->status == self::DOCUMENT_STATUS_NEEDS_APPROVE;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->status == self::DOCUMENT_STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isOnHold()
    {
        return $this->status == self::DOCUMENT_STATUS_ON_HOLD;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status == self::DOCUMENT_STATUS_CANCELLED;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->status == self::DOCUMENT_STATUS_REGISTERED;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRemoveSignatories(User $user)
    {
        return ($this->isNeedsFixing() || $this->isNeedsApprove()) &&
            ($this->isOwner($user));
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::BLANK_ANALOG => self::BLANK_ANALOG,
            self::BLANK_DIGITAL => self::BLANK_DIGITAL,
        ];
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::DOCUMENT_STATUS_NEW => 'document.new',
            self::DOCUMENT_STATUS_ON_HOLD => 'document.on_hold',
            self::DOCUMENT_STATUS_NEEDS_FIXING => 'document.needs_fixing',
            self::DOCUMENT_STATUS_NEEDS_APPROVE => 'document.needs_approve',
            self::DOCUMENT_STATUS_APPROVED => 'document.approved',
            self::DOCUMENT_STATUS_REGISTERED => 'document.registered',
            self::DOCUMENT_STATUS_CANCELLED => 'document.cancelled',
        ];
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
     * @return array
     */
    public function getPriorityLabels()
    {
        return [
            1 => 'success',
            2 => 'warning',
            3 => 'inverse',
            4 => 'primary',
            5 => 'success',
            6 => 'success',
            7 => 'danger',
            8 => 'primary',
            9 => 'success'
        ];
    }

    /**
     * @return boolean
     */
    public function canEditTextDocument()
    {
        if (!in_array($this->getStatus(), [
                Document::DOCUMENT_STATUS_APPROVED,
                Document::DOCUMENT_STATUS_REGISTERED
        ])) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canExportDocument(User $user)
    {
        return $this->owner->getId() == $user->getId()
            || $this->curator->getId() == $user->getId()
            || $this->project->getLeader()->getId() == $user->getId()
            || $this->getSignatory($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkGrants(User $user)
    {
        return $this->owner->getId() == $user->getId()
            || $this->curator->getId() == $user->getId()
            || $this->project->getLeader()->getId() == $user->getId()
            || $this->getSignatory($user)
            || $user->canViewAllDocuments()
            || ($this->owner->getTeam()->getId() == $user->getTeam()->getId())
        ;
    }
}