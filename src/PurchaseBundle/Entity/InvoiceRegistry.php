<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 05.02.19
 * Time: 12:28
 */

namespace PurchaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice_registry")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\InvoiceRegistryRepository")
 */
class InvoiceRegistry
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="money_limit", type="float", nullable=true)
     */
    private $moneyLimit;

    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="invoiceRegistry", cascade="all")
     */
    private $invoices;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * Invoice Registry constructor.
     */
    public function __construct()
    {
        $this->invoices = new ArrayCollection();
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return InvoiceRegistry
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getMoneyLimit()
    {
        return $this->moneyLimit;
    }

    /**
     * @param int $moneyLimit
     * @return InvoiceRegistry
     */
    public function setMoneyLimit($moneyLimit)
    {
        $this->moneyLimit = $moneyLimit;
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
     * @return InvoiceRegistry
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;
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
     * @return InvoiceRegistry
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @return InvoiceRegistry
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }
}