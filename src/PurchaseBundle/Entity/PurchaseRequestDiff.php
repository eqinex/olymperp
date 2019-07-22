<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseRequestDiff
 *
 * @ORM\Table(name="purchase_request_diff")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\PurchaseRequestDiffRepository")
 */
class PurchaseRequestDiff
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
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

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
     * @return PurchaseRequestDiff
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
     * Set PurchaseRequest
     *
     * @param PurchaseRequest $purchaseRequest
     *
     * @return PurchaseRequestDiff
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * Get PurchaseRequest
     *
     * @return PurchaseRequest
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return PurchaseRequestDiff
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
     * @return PurchaseRequestDiff
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
     * @return PurchaseRequestDiff
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
     * @return PurchaseRequestDiff
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}

