<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DayOff
 *
 * @ORM\Table(name="day_off")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DayOffRepository")
 */
class DayOff
{
    const TYPE_VACATIONS = 'vacations';
    const TYPE_MISS = 'miss';
    const TYPE_OVERTIME = 'overtime';
    const TYPE_ILLNESS = 'illness';
    const TYPE_BUSINESS_TRIP = 'business_trip';

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
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStart", type="datetime")
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnd", type="datetime")
     */
    private $dateEnd;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * DayOff constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->dateStart = new \DateTime();
        $this->dateEnd = new \DateTime();
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
     * Set owner
     *
     * @param \stdClass $owner
     *
     * @return DayOff
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \stdClass
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return DayOff
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return DayOff
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return DayOff
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return DayOff
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
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_BUSINESS_TRIP => self::TYPE_BUSINESS_TRIP,
            self::TYPE_ILLNESS => self::TYPE_ILLNESS,
            self::TYPE_MISS => self::TYPE_MISS,
            self::TYPE_OVERTIME => self::TYPE_OVERTIME,
            self::TYPE_VACATIONS => self::TYPE_VACATIONS,
        ];
    }
}

