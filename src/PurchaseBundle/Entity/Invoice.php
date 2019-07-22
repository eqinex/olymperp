<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 05.02.19
 * Time: 12:28
 */

namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\InvoiceRepository")
 */
class Invoice
{
    const STATUS_NEW = 'new';
    const STATUS_READY_TO_PAY = 'ready_to_pay';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';
    const STATUS_OUTDATED = 'outdated';

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
     * @ORM\Column(name="invoice_number", type="string", length=255)
     */
    private $invoiceNumber;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id", nullable=true)
     */
    private $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;
    /**
     * @var int
     *
     * @ORM\Column(name="amount_paid", type="float", nullable=true)
     */
    private $amountPaid;

    /**
     * @ORM\OneToMany(targetEntity="PurchaseBundle\Entity\RequestItem", mappedBy="invoice", cascade="all")
     */
    private $requestItems;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
    
    /**
     * @ORM\ManyToOne(targetEntity="RequestFile")
     * @ORM\JoinColumn(name="invoice_file_id", referencedColumnName="id")
     */
    private $invoiceFile;

    /**
     * @ORM\ManyToOne(targetEntity="InvoiceRegistry")
     * @ORM\JoinColumn(name="invoice_registry_id", referencedColumnName="id")
     */
    private $invoiceRegistry;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * Activity constructor.
     */
    public function __construct()
    {
        $this->requestItems = new ArrayCollection();
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
     * @return PurchaseRequest
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     *
     * @return Invoice
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     *
     * @return Invoice
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param Supplier $supplier
     * @return Invoice
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Invoice
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param float $amount
     *
     * @return Invoice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @param int $amountPaid
     * @return Invoice
     */
    public function setAmountPaid($amountPaid)
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestItems()
    {
        return $this->requestItems;
    }

    /**
     * @param mixed $requestItems
     * @return Invoice
     */
    public function setRequestItems($requestItems)
    {
        $this->requestItems = $requestItems;
        return $this;
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
     * @return Invoice
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoiceFile()
    {
        return $this->invoiceFile && !$this->invoiceFile->isDeleted() ? $this->invoiceFile : null;
    }

    /**
     * @param mixed $invoiceFile
     * @return Invoice
     */
    public function setInvoiceFile($invoiceFile)
    {
        $this->invoiceFile = $invoiceFile;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoiceRegistry()
    {
        return $this->invoiceRegistry;
    }

    /**
     * @param mixed $invoiceRegistry
     * @return Invoice
     */
    public function setInvoiceRegistry($invoiceRegistry)
    {
        $this->invoiceRegistry = $invoiceRegistry;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return Invoice
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isInvoiceOwner(User $user)
    {
        return $this->getOwner() && $this->getOwner()->getId() == $user->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRemoveInvoice(User $user)
    {
        if ($this->isInvoiceOwner($user) || $this->getPurchaseRequest()->isPurchasingManager($user) || $this->getPurchaseRequest()->isPurchasingLeader($user)) {
                return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canInvoicePaid(User $user)
    {
        return $user->canEditRegistry() && $this->getStatus() == Invoice::STATUS_READY_TO_PAY;
    }
}