<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User;

/**
 * InvoiceRegistryDiff
 *
 * @ORM\Table(name="registry_diff")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\InvoiceRegistryDiffRepository")
 */
class InvoiceRegistryDiff
{
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
     * @ORM\JoinColumn(name="changed_by_id", referencedColumnName="id")
     */
    private $changedBy;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\InvoiceRegistry")
     * @ORM\JoinColumn(name="invoice_registry_id", referencedColumnName="id")
     */
    private $invoiceRegistry;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=255)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="text", nullable=true)
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="text", nullable=true)
     */
    private $newValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
     * Set changedBy
     *
     * @param User $changedBy
     *
     * @return InvoiceRegistryDiff
     */
    public function setChangedBy($changedBy)
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    /**
     * Get changedBy
     *
     * @return User
     */
    public function getChangedBy()
    {
        return $this->changedBy;
    }

    /**
     * Set invoiceRegistry
     *
     * @param InvoiceRegistry $invoiceRegistry
     *
     * @return InvoiceRegistryDiff
     */
    public function setInvoiceRegistry($invoiceRegistry)
    {
        $this->invoiceRegistry = $invoiceRegistry;

        return $this;
    }

    /**
     * Get invoiceRegistry
     *
     * @return InvoiceRegistry
     */
    public function getInvoiceRegistry()
    {
        return $this->invoiceRegistry;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return InvoiceRegistryDiff
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     *
     * @return InvoiceRegistryDiff
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return InvoiceRegistryDiff
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * 
     * @return InvoiceRegistryDiff
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}

