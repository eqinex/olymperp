<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\City;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseRequestDelivery
 *
 * @ORM\Table(name="purchase_request_delivery")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\PurchaseRequestDeliveryRepository")
 */
class PurchaseRequestDelivery
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
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\RequestItem")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_from_id", referencedColumnName="id")
     */
    private $cityFrom;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_where_id", referencedColumnName="id")
     */
    private $cityWhere;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id.
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
     * @return PurchaseRequestDelivery
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * @return RequestItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param RequestItem $requestItem
     *
     * @return PurchaseRequestDelivery
     */
    public function setItem($requestItem)
    {
        $this->item = $requestItem;

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
     * @param Supplier $supplier
     *
     * @return PurchaseRequestDelivery
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @return City
     */
    public function getCityFrom()
    {
        return $this->cityFrom;
    }

    /**
     * @param City $cityFrom
     *
     * @return PurchaseRequestDelivery
     */
    public function setCityFrom($cityFrom)
    {
        $this->cityFrom = $cityFrom;

        return $this;
    }

    /**
     * @return City
     */
    public function getCityWhere()
    {
        return $this->cityWhere;
    }

    /**
     * @param City $cityWhere
     *
     * @return PurchaseRequestDelivery
     */
    public function setCityWhere($cityWhere)
    {
        $this->cityWhere = $cityWhere;

        return $this;
    }

    /**
     * @param float $price
     *
     * @return PurchaseRequestDelivery
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return PurchaseRequestDelivery
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
     * @param User $owner
     * @return PurchaseRequestDelivery
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


}
