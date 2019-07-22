<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestItem
 *
 * @ORM\Table(name="purchase_request_item")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\RequestItemRepository")
 */
class RequestItem
{
    const PRODUCTION_STATUS_IN_PRODUCTION = 'in_production';
    const PRODUCTION_STATUS_PRODUCED = 'produced';

    const STOCK_STATUS_ON_STOCK = 'on_stock';

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
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="text")
     */
    private $sku;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="actual_quantity", type="integer", nullable=true)
     */
    private $actualQuantity;
    
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Unit")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var float
     *
     * @ORM\Column(name="price_without_vat", type="float", nullable=true)
     */
    private $priceWithoutVat;

    /**
     * @var float
     *
     * @ORM\Column(name="vat_amount", type="float", nullable=true)
     */
    private $vatAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="total_price", type="float", nullable=true)
     */
    private $totalPrice;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequestCategory")
     * @ORM\JoinColumn(name="purchase_request_category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\SuppliesCategory")
     * @ORM\JoinColumn(name="supplies_category_id", referencedColumnName="id")
     */
    private $suppliesCategory;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

    /**
     * @var string
     *
     * @ORM\Column(name="notice", type="text", nullable=true)
     */
    private $notice;

    /**
     * @ORM\Column(type="datetime", name="preferred_shipment_date", nullable=true)
     */
    private $preferredShipmentDate;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="prepayment_amount", type="integer", nullable=true)
     */
    private $prepaymentAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="estimated_shipment_time", type="integer", nullable=true)
     */
    private $estimatedShipmentTime;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_nr", type="text", nullable=true)
     */
    private $invoiceNumber;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var string
     *
     * @ORM\Column(name="production_status", type="text", nullable=true)
     */
    private $productionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="stock_status", type="text", nullable=true)
     */
    private $stockStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="on_stock_at", nullable=true)
     */
    private $onStockAt;

    /**
     * @var float
     *
     * @ORM\Column(name="preliminary_estimate", type="float", nullable=true)
     */
    private $preliminaryEstimate;

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
     * Set title
     *
     * @param string $title
     *
     * @return RequestItem
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return RequestItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->getPurchaseRequest()->getNumberOfProducts() ? $this->getPurchaseRequest()->getNumberOfProducts() * $this->quantity : $this->quantity;
    }

    /**
     * Set unit
     *
     * @param Unit $unit
     *
     * @return RequestItem
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set priceWithoutVat
     *
     * @param float $priceWithoutVat
     *
     * @return RequestItem
     */
    public function setPriceWithoutVat($priceWithoutVat)
    {
        $this->priceWithoutVat = $priceWithoutVat;

        return $this;
    }

    /**
     * Get priceWithoutVat
     *
     * @return float
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }

    /**
     * Set vatAmount
     *
     * @param float $vatAmount
     *
     * @return RequestItem
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;

        return $this;
    }

    /**
     * Get vatAmount
     *
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * Set totalPrice
     *
     * @param float $totalPrice
     *
     * @return RequestItem
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return PurchaseRequestCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param PurchaseRequestCategory $category
     * 
     * @return RequestItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
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
     * @return RequestItem
     */
    public function setSuppliesCategory($suppliesCategory)
    {
        $this->suppliesCategory = $suppliesCategory;
        return $this;
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
     * @return RequestItem
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return RequestItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param string $notice
     * @return RequestItem
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
        return $this;
    }

    /**
     * @return \DateTime()
     */
    public function getPreferredShipmentDate()
    {
        return $this->preferredShipmentDate;
    }

    /**
     * @param \DateTime() $preferredShipmentDate
     * @return RequestItem
     */
    public function setPreferredShipmentDate($preferredShipmentDate)
    {
        $this->preferredShipmentDate = $preferredShipmentDate;
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
     * @return RequestItem
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
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
     * @param float $price
     * @return RequestItem
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrepaymentAmount()
    {
        return $this->prepaymentAmount;
    }

    /**
     * @param float $prepaymentAmount
     * @return RequestItem
     */
    public function setPrepaymentAmount($prepaymentAmount)
    {
        $this->prepaymentAmount = $prepaymentAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getPreliminaryEstimate()
    {
        return $this->preliminaryEstimate;
    }

    /**
     * @param float $preliminaryEstimate
     * @return RequestItem
     */
    public function setPreliminaryEstimate($preliminaryEstimate)
    {
        $this->preliminaryEstimate = $preliminaryEstimate;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstimatedShipmentTime()
    {
        return $this->estimatedShipmentTime;
    }

    /**
     * @param string $estimatedShipmentTime
     * @return RequestItem
     */
    public function setEstimatedShipmentTime($estimatedShipmentTime)
    {
        $this->estimatedShipmentTime = $estimatedShipmentTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActualQuantity()
    {
        return $this->actualQuantity;
    }

    /**
     * @param mixed $actualQuantity
     * @return RequestItem
     */
    public function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity = $actualQuantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return RequestItem
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Invoice $invoice
     * @return RequestItem
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductionStatus()
    {
        return $this->productionStatus;
    }

    /**
     * @param string $productionStatus
     * @return RequestItem
     */
    public function setProductionStatus($productionStatus)
    {
        $this->productionStatus = $productionStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getStockStatus()
    {
        return $this->stockStatus;
    }

    /**
     * @param string $stockStatus
     * @return RequestItem
     */
    public function setStockStatus($stockStatus)
    {
        $this->stockStatus = $stockStatus;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOnStockAt()
    {
        return $this->onStockAt;
    }

    /**
     * @param \DateTime $onStockAt
     * @return RequestItem
     */

    public function setOnStockAt($onStockAt)
    {
        $this->onStockAt = $onStockAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getSingleProductQuantity()
    {
        return $this->getPurchaseRequest()->getNumberOfProducts() ? $this->getQuantity() / $this->getPurchaseRequest()->getNumberOfProducts() : $this->getQuantity();
    }
}
