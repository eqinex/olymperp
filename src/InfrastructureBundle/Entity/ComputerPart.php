<?php

namespace InfrastructureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComputerPart
 *
 * @ORM\Table(name="computer_part")
 * @ORM\Entity(repositoryClass="InfrastructureBundle\Repository\ComputerPartRepository")
 */
class ComputerPart
{
    const TYPE_MONITOR = 'monitor';
    const TYPE_KEYBOARD = 'keyboard';
    const TYPE_MOUSE = 'mouse';
    const TYPE_PROCESSOR = 'processor';
    const TYPE_RAM = 'ram';
    const TYPE_MOTHERBOARD = 'motherboard';
    const TYPE_VIDEO_CARD = 'video_card';
    const TYPE_HDD = 'hdd';
    const TYPE_OPERATION_SYSTEM = 'operation_system';

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number", type="string", nullable=true)
     */
    private $serialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="inventory_number", type="string", nullable=true)
     */
    private $inventoryNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true, options={"default":"0"})
     */
    private $deleted = 0;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ComputerPart
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $name
     * @return ComputerPart
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     * @return ComputerPart
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $serialNumber
     * @return ComputerPart
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param string $inventoryNumber
     * @return ComputerPart
     */
    public function setInventoryNumber($inventoryNumber)
    {
        $this->inventoryNumber = $inventoryNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getInventoryNumber()
    {
        return $this->inventoryNumber;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     * @return ComputerPart
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_HDD => self::TYPE_HDD,
            self::TYPE_KEYBOARD => self::TYPE_KEYBOARD,
            self::TYPE_MONITOR => self::TYPE_MONITOR,
            self::TYPE_MOTHERBOARD => self::TYPE_MOTHERBOARD,
            self::TYPE_MOUSE => self::TYPE_MOUSE,
            self::TYPE_OPERATION_SYSTEM => self::TYPE_OPERATION_SYSTEM,
            self::TYPE_PROCESSOR => self::TYPE_PROCESSOR,
            self::TYPE_RAM => self::TYPE_RAM,
            self::TYPE_VIDEO_CARD => self::TYPE_VIDEO_CARD
        ];
    }
}