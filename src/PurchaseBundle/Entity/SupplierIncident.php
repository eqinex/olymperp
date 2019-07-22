<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierIncident
 *
 * @ORM\Table(name="supplier_incident")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\SupplierIncidentRepository")
 */
class SupplierIncident
{
    const CRITICALITY_LOW = 0;
    const CRITICALITY_NORMAL = 1;
    const CRITICALITY_HIGH = 2;
    const CRITICALITY_BIG = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="criticality", type="string", length=255)
     */
    private $criticality;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * SupplierIncident constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param Supplier $supplier
     * @return SupplierIncident
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return SupplierIncident
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set criticality.
     *
     * @param string $criticality
     *
     * @return SupplierIncident
     */
    public function setCriticality($criticality)
    {
        $this->criticality = $criticality;

        return $this;
    }

    /**
     * Get criticality.
     *
     * @return string
     */
    public function getCriticality()
    {
        return $this->criticality;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return SupplierIncident
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public static function getCriticalityChoices()
    {
        return [
            self::CRITICALITY_LOW => 'low',
            self::CRITICALITY_NORMAL => 'normal',
            self::CRITICALITY_HIGH => 'high',
            self::CRITICALITY_BIG => 'big',
        ];
    }

    /**
     * @return array
     */
    public function getCriticalityLabels()
    {
        return [
            self::CRITICALITY_LOW => 'success',
            self::CRITICALITY_NORMAL => 'primary',
            self::CRITICALITY_HIGH => 'warning',
            self::CRITICALITY_BIG => 'danger'
        ];
    }
}