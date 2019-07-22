<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\PurchaseRequestCategory;

/**
 * PriceIteration
 *
 * @ORM\Table(name="price_iteration")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PriceIterationRepository")
 */
class PriceIteration
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
     * @ORM\ManyToOne(targetEntity="ProjectPrice")
     * @ORM\JoinColumn(name="project_price_id", referencedColumnName="id")
     */
    private $projectPrice;

    /**
     * @ORM\OneToMany(targetEntity="CategoryPrice", mappedBy="priceIteration", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $categoryPrices;

    /**
     * PriceIteration constructor.
     */
    public function __construct()
    {
        $this->categoryPrices = new ArrayCollection();
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
     * Set projectPrice.
     *
     * @param ProjectPrice $projectPrice
     *
     * @return PriceIteration
     */
    public function setProjectPrice($projectPrice)
    {
        $this->projectPrice = $projectPrice;

        return $this;
    }

    /**
     * Get projectPrice.
     *
     * @return ProjectPrice
     */
    public function getProjectPrice()
    {
        return $this->projectPrice;
    }

    /**
     * @return mixed
     */
    public function getCategoryPrices()
    {
        return $this->categoryPrices;
    }

    /**
     * @param mixed $categoryPrices
     * @return PriceIteration
     */
    public function setCategoryPrices($categoryPrices)
    {
        $this->categoryPrices = $categoryPrices;
        return $this;
    }
}
