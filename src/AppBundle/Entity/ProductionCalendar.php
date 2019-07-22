<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 13.06.19
 * Time: 11:22
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductionCalendar
 *
 * @ORM\Table(name="production_calendar")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductionCalendarRepository")
 */
class ProductionCalendar
{
    const TYPE_WORKING_DAY = 'working_day';
    const TYPE_HOLIDAY = 'holiday';
    const TYPE_SHORTENED_DAY = 'shortened_day';

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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * Production calendar constructor.
     */
    public function __construct()
    {
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
     * Set type
     *
     * @param string $type
     *
     * @return ProductionCalendar
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ProductionCalendar
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return ProductionCalendar
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
     * @return ProductionCalendar
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
     * @return array
     */
    public static function getProductionCalendarTypes()
    {
        return [
            self::TYPE_WORKING_DAY => self::TYPE_WORKING_DAY,
            self::TYPE_SHORTENED_DAY => self::TYPE_SHORTENED_DAY,
            self::TYPE_HOLIDAY => self::TYPE_HOLIDAY
        ];
    }

}