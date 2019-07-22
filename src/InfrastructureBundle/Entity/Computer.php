<?php

namespace InfrastructureBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Computer
 *
 * @ORM\Table(name="computer")
 * @ORM\Entity(repositoryClass="InfrastructureBundle\Repository\ComputerRepository")
 */
class Computer
{
    const TYPE_LAPTOP = 'laptop';
    const TYPE_DESKTOP_COMPUTER = 'desktop_computer';
    const TYPE_MONOBLOCK = 'monoblock';

    const TYPE_IP_DHCP = 'dhcp';
    const TYPE_IP_STATIC = 'static';
    const TYPE_IP_BIND = 'bind';

    const SERVER_TYPE_HOST = 'host';
    const SERVER_TYPE_GUEST = 'guest';
    const SERVER_TYPE_DEDICATED = 'dedicated_server';

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
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     */
    private $employee;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=255, nullable=true)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_type", type="string", length=60)
     */
    private $ipType;

    /**
     * @var string
     *
     * @ORM\Column(name="mac_address", type="string", length=255, nullable=true)
     */
    private $macAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=60, nullable=true)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=60)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255, nullable=true)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="key_in_system", type="string", length=255, nullable=true)
     */
    private $keyInSystem;

    /**
     * @var string
     *
     * @ORM\Column(name="key_on_sticker", type="string", length=255, nullable=true)
     */
    private $keyOnSticker;

    /**
     * @var boolean
     *
     * @ORM\Column(name="legal", type="boolean")
     */
    private $legal = true;

    /**
     * @var string
     *
     * @ORM\Column(name="inventory_number", type="string", nullable=true)
     */
    private $inventoryNumber;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="operation_system", referencedColumnName="id")
     */
    private $operationSystem;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="processor", referencedColumnName="id")
     */
    private $processor;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="ram", referencedColumnName="id")
     */
    private $ram;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="motherboard", referencedColumnName="id")
     */
    private $motherboard;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="videocard", referencedColumnName="id")
     */
    private $videoCard;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="hdd_first", referencedColumnName="id")
     */
    private $hddFirst;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="hdd_second", referencedColumnName="id", nullable=true)
     */
    private $hddSecond;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="keyboard", referencedColumnName="id")
     */
    private $keyboard;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="mouse", referencedColumnName="id")
     */
    private $mouse;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255)
     */
    private $host = false;

    /**
     * @var string
     *
     * @ORM\Column(name="installed_service", type="string", length=255, nullable=true)
     */
    private $installedService;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number", type="string", length=255, nullable=true)
     */
    private $serialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=255, nullable=true)
     */
    private $room;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true, options={"default":"0"})
     */
    private $deleted = 0;

    /**
     * @ORM\OneToMany(targetEntity="ComputerParts", mappedBy="computer", cascade="persist")
     */
    private $computerParts;

    /**
     * @var string
     *
     * @ORM\Column(name="cartridge_type", type="string", length=255, nullable=true)
     */
    private $cartridgeType;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address_computer", type="string", length=255, nullable=true)
     */
    private $ipAddressComputer;

    /**
     * @var string
     *
     * @ORM\Column(name="mac_address_computer", type="string", length=255, nullable=true)
     */

    private $macAddressComputer;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=60, nullable=true)
     */

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
     * @return Computer
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return Computer
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
    
    /**
     * @param User $employee
     * @return $this
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
        return $this;
    }

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param string $name
     * @return Computer
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

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param string $ipAddress
     * @return Computer
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddressComputer
     * @return Computer
     */
    public function setIpAddressComputer($ipAddressComputer)
    {
        $this->ipAddressComputer = json_encode(preg_split('/[,]+/', $ipAddressComputer));

        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddressComputer()
    {
        return json_decode($this->ipAddressComputer);
    }

    /**
     * @return string
     */
    public function getIpType()
    {
        return $this->ipType;
    }

    /**
     * @param string $ipType
     * @return Computer
     */
    public function setIpType($ipType)
    {
        $this->ipType = $ipType;
        return $this;
    }

    /**
     * @param string $macAddress
     * @return Computer
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param string $macAddressComputer
     * @return Computer
     */
    public function setMacAddressComputer($macAddressComputer)
    {
        $this->macAddressComputer = json_encode(preg_split('/[,]+/', $macAddressComputer));

        return $this;
    }

    /**
     * @return string
     */
    public function getMacAddressComputer()
    {
        return json_decode($this->macAddressComputer);
    }

    /**
     * @param string $domain
     * @return Computer
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $keyInSystem
     * @return Computer
     */
    public function setKeyInSystem($keyInSystem)
    {
        $this->keyInSystem = $keyInSystem;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyInSystem()
    {
        return $this->keyInSystem;
    }

    /**
     * @param string $keyOnSticker
     * @return Computer
     */
    public function setKeyOnSticker($keyOnSticker)
    {
        $this->keyOnSticker = $keyOnSticker;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyOnSticker()
    {
        return $this->keyOnSticker;
    }

    /**
     * @return boolean
     */
    public function isLegal()
    {
        return $this->legal;
    }

    /**
     * @param boolean $legal
     * @return Computer
     */
    public function setLegal($legal)
    {
        $this->legal = $legal;
        return $this;
    }

    /**
     * @param string $inventoryNumber
     * @return Computer
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
     * @param ComputerPart $operationSystem
     * @return $this
     */
    public function setOperationSystem($operationSystem)
    {
        $this->operationSystem = $operationSystem;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getOperationSystem()
    {
        return $this->operationSystem;
    }


    /**
     * @param ComputerPart $processor
     * @return $this
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @param ComputerPart $ram
     * @return $this
     */
    public function setRam($ram)
    {
        $this->ram = $ram;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getRam()
    {
        return $this->ram;
    }

    /**
     * @param ComputerPart $motherboard
     * @return $this
     */
    public function setMotherboard($motherboard)
    {
        $this->motherboard = $motherboard;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getMotherboard()
    {
        return $this->motherboard;
    }

    /**
     * @param ComputerPart $videoCard
     * @return $this
     */
    public function setVideoCard($videoCard)
    {
        $this->videoCard = $videoCard;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getVideoCard()
    {
        return $this->videoCard;
    }

    /**
     * @param ComputerPart $hddFirst
     * @return $this
     */
    public function setHddFirst($hddFirst)
    {
        $this->hddFirst = $hddFirst;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getHddFirst()
    {
        return $this->hddFirst;
    }

    /**
     * @param ComputerPart $hddSecond
     * @return $this
     */
    public function setHddSecond($hddSecond)
    {
        $this->hddSecond = $hddSecond;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getHddSecond()
    {
        return $this->hddSecond;
    }

    /**
     * @param ComputerPart $keyboard
     * @return $this
     */
    public function setKeyboard($keyboard)
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getKeyboard()
    {
        return $this->keyboard;
    }

    /**
     * @param ComputerPart $mouse
     * @return $this
     */
    public function setMouse($mouse)
    {
        $this->mouse = $mouse;
        return $this;
    }

    /**
     * @return ComputerPart
     */
    public function getMouse()
    {
        return $this->mouse;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Computer
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstalledService()
    {
        return $this->installedService;
    }

    /**
     * @param string $installedService
     * @return Computer
     */
    public function setInstalledService($installedService)
    {
        $this->installedService = $installedService;

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
     * @param string $serialNumber
     * @return Computer
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     * @return Computer
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
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
     * @return Computer
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return ComputerParts[]
     */
    public function getComputerParts()
    {
        return $this->computerParts;
    }

    /**
     * @param ComputerParts $computerParts
     * @return Computer
     */
    public function setComputerParts($computerParts)
    {
        $this->computerParts = $computerParts;
        return $this;
    }

    /**
     * @return string
     */
    public function getCartridgeType()
    {
        return $this->cartridgeType;
    }

    /**
     * @param string $cartridgeType
     * @return Computer
     */
    public function setCartridgeType($cartridgeType)
    {
        $this->cartridgeType = $cartridgeType;
        return $this;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return Computer
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
        return $this->quantity;
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_DESKTOP_COMPUTER => self::TYPE_DESKTOP_COMPUTER,
            self::TYPE_LAPTOP => self::TYPE_LAPTOP,
            self::TYPE_MONOBLOCK => self::TYPE_MONOBLOCK
        ];
    }

    /**
     * @return array
     */
    public static function getIpTypesList()
    {
        return [
            self::TYPE_IP_BIND => self::TYPE_IP_BIND,
            self::TYPE_IP_DHCP => self::TYPE_IP_DHCP,
            self::TYPE_IP_STATIC => self::TYPE_IP_STATIC
        ];
    }

    /**
     * @return array
     */
    public static function getServerTypesList()
    {
        return [
            self::SERVER_TYPE_HOST => self::SERVER_TYPE_HOST,
            self::SERVER_TYPE_GUEST => self::SERVER_TYPE_GUEST,
            self::SERVER_TYPE_DEDICATED => self::SERVER_TYPE_DEDICATED
        ];
    }

    /**
     * @return array
     */
    public function getTiedComputerParts()
    {
        $parts = [];

        foreach ($this->getComputerParts() as $computerPart) {
            $parts[] = $computerPart->getPart()->getId();
        }

        return $parts;
    }

    /**
     * @return array
     */
    public function getTiedComputerPartsName()
    {
        $parts = [];

        /** @var ComputerPart $computerPart */
        foreach ($this->getComputerParts() as $computerPart) {
            $parts[] = ['name' => $computerPart->getPart()->getName(), 'inventoryNumber' => $computerPart->getPart()->getInventoryNumber()];
        }

        return $parts;
    }

}