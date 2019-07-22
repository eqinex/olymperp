<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuppliesCategory
 *
 * @ORM\Table(name="supplies_category")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\SuppliesCategoryRepository")
 */
class SuppliesCategory
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title = '';

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\SuppliesCategory")
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")
     */
    private $parentCategory;


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
     * @return PurchaseRequestCategory
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
     * Set title
     *
     * @param string $title
     *
     * @return PurchaseRequestCategory
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
     * @return mixed
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    /**
     * @param SuppliesCategory $parentCategory
     * @return SuppliesCategory
     */
    public function setParentCategory($parentCategory)
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}

