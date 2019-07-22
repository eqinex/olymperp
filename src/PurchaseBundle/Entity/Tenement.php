<?php


namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * tenement
 *
 * @ORM\Table(name="tenement")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\TenementRepository")
 */
class Tenement
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
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var string
     *
     * @ORM\Column(name="rent", type="float")
     */
    private $rent;

    /**
     * @var string
     *
     * @ORM\Column(name="heating", type="float")
     */
    private $heating;

    /**
     * @var string
     *
     * @ORM\Column(name="communal_payments", type="float")
     */
    private $communalPayments;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="square", type="float")
     */
    private $square;



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     *
     * @return $this
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return string
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * @param string $rent
     *
     * @return string
     */
    public function setRent($rent)
    {
        $this->rent = $rent;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeating()
    {
        return $this->heating;
    }

    /**
     * @param string $heating
     *
     * @return string
     */
    public function setHeating($heating)
    {
        $this->heating = $heating;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommunalPayments()
    {
        return $this->communalPayments;
    }

    /**
     * @param string $communalPayments
     *
     * @return string
     */
    public function setCommunalPayments($communalPayments)
    {
        $this->communalPayments = $communalPayments;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     *
     * @return string
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return string
     */
    public function getSquare()
    {
        return $this->square;
    }

    /**
     * @param string $square
     *
     * @return string
     */
    public function setSquare($square)
    {
        $this->square = $square;

        return $this;
    }

}