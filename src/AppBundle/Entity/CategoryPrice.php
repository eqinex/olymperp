<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\PurchaseRequestCategory;

/**
 * CategoryPrice
 *
 * @ORM\Table(name="category_price")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryPriceRepository")
 */
class CategoryPrice
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
     * @ORM\ManyToOne(targetEntity="PriceIteration")
     * @ORM\JoinColumn(name="price_iteration_id", referencedColumnName="id")
     */
    private $priceIteration;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequestCategory")
     * @ORM\JoinColumn(name="request_category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;


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
     * Set priceIteration.
     *
     * @param PriceIteration $priceIteration
     *
     * @return CategoryPrice
     */
    public function setPriceIteration($priceIteration)
    {
        $this->priceIteration = $priceIteration;

        return $this;
    }

    /**
     * Get priceIteration.
     *
     * @return PriceIteration
     */
    public function getPriceIteration()
    {
        return $this->priceIteration;
    }

    /**
     * Set category.
     *
     * @param PurchaseRequestCategory $category
     *
     * @return CategoryPrice
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return PurchaseRequestCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set price.
     *
     * @param float $price
     *
     * @return CategoryPrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }
}
