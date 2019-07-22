<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.02.19
 * Time: 9:14
 */

namespace ProductionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;

/**
 * Serial
 *
 * @ORM\Table(name="serial")
 * @ORM\Entity(repositoryClass="ProductionBundle\Repository\SerialRepository")
 */
class Serial
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
     * @ORM\ManyToOne(targetEntity="ProductionBundle\Entity\SerialCategory")
     * @ORM\JoinColumn(name="serial_category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="ProductionBundle\Entity\Ware")
     * @ORM\JoinColumn(name="ware_id", referencedColumnName="id")
     */
    private $ware;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="PurchaseBundle\Entity\RequestItem", cascade={"all"})
     * @ORM\JoinTable(name="serial_items",
     *      joinColumns={@ORM\JoinColumn(name="serial_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $serialItems;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->serialItems = new ArrayCollection();
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
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param SerialCategory $category
     * @return Serial
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWare()
    {
        return $this->ware;
    }

    /**
     * @param Ware $ware
     * @return Serial
     */
    public function setWare($ware)
    {
        $this->ware = $ware;
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Serial
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
     * @return ArrayCollection
     */
    public function getSerialItems()
    {
        return $this->serialItems;
    }

    /**
     * @param $serialItems
     * @return $this
     */
    public function setSerialItems($serialItems)
    {
        $this->serialItems = $serialItems;
        return $this;
    }

    /**
     * @param RequestItem $item
     * @return $this
     */
    public function addSerialItem(RequestItem $item)
    {
        if (!$this->serialItems->contains($item)) {
            $this->serialItems->add($item);
        }

        return $this;
    }

    /**
     * @param RequestItem $item
     */
    public function removeSerialItem(RequestItem $item)
    {
        $this->serialItems->removeElement($item);
    }

    /**
     * @return array
     */
    public function getSerialItemsIds()
    {
        $ids = [];

        /** @var RequestItem $item */
        foreach ($this->getSerialItems() as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }
}