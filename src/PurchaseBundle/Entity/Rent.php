<?php


namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * rent
 *
 * @ORM\Table(name="rent")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\RentRepository")
 */
class Rent
{
    const PAYMENT_METHOD_CASH = "cash";
    const PAYMENT_METHOD_CARD = "card";

    const MONTH_JANUARY = 'January';
    const MONTH_FEBRUARY = 'February';
    const MONTH_MARCH = 'March';
    const MONTH_APRIL = 'April';
    const MONTH_MAY = 'May';
    const MONTH_JUNE = 'June';
    const MONTH_JULY = 'July';
    const MONTH_AUGUST = 'August';
    const MONTH_SEPTEMBER = 'September';
    const MONTH_OCTOBER = 'October';
    const MONTH_NOVEMBER = 'November';
    const MONTH_DECEMBER = 'December';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tenement")
     * @ORM\JoinColumn(name="tenement_id", referencedColumnName="id")
     */
    private $tenement;

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
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string")
     */
    private $method;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     */
    private $employee;

    /**
     * Get id\
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get tenement
     *
     * @return Tenement
     */
    public function getTenement()
    {
        return $this->tenement;
    }

    /**
     * Set tenement
     *
     * @param Tenement $tenement
     *
     * @return mixed
     */
    public function setTenement($tenement)
    {
        $this->tenement = $tenement;

        return $this;
    }

    /**
     * Get rent
     *
     * @return float
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * Set rent
     *
     * @param float $rent
     *
     * @return $this
     */
    public function setRent($rent)
    {
        $this->rent = $rent;

        return $this;
    }

    /**
     * Get heating
     *
     * @return float
     */
    public function getHeating()
    {
        return $this->heating;
    }

    /**
     * Set heating
     *
     * @param float $heating
     *
     * @return $this
     */
    public function setHeating($heating)
    {
        $this->heating = $heating;

        return $this;
    }

    /**
     * Get communalPayments
     *
     * @return float
     */
    public function getCommunalPayments()
    {
        return $this->communalPayments;
    }

    /**
     * Set communalPayments
     *
     * @param float $communalPayments
     *
     * @return $this
     */
    public function setCommunalPayments($communalPayments)
    {
        $this->communalPayments = $communalPayments;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get square
     *
     * @return float
     */
    public function getSquare()
    {
        return $this->square;
    }

    /**
     * Set square
     *
     * @param float $square
     *
     * @return $this
     */
    public function setSquare($square)
    {
        $this->square = $square;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set  method
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Rent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get employee
     *
     * @return $this
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set employee
     *
     * @param User $employee
     *
     * @return Rent
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get paymentMethodList
     *
     * @return array
     */
    public function getPaymentMethodList()
    {
        return [
            self::PAYMENT_METHOD_CASH,
            self::PAYMENT_METHOD_CARD
        ];
    }

    /**
     * Get monthList
     *
     * @return array
     */
    public function getMonthList()
    {
        return [
            self::MONTH_JANUARY => '01',
            self::MONTH_FEBRUARY => '02',
            self::MONTH_MARCH => '03',
            self::MONTH_APRIL => '04',
            self::MONTH_MAY => '05',
            self::MONTH_JUNE => '06',
            self::MONTH_JULY => '07',
            self::MONTH_AUGUST => '08',
            self::MONTH_SEPTEMBER => '09',
            self::MONTH_OCTOBER => '10',
            self::MONTH_NOVEMBER => '11',
            self::MONTH_DECEMBER => '12'
        ];
    }
}