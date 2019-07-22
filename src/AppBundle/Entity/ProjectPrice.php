<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\PurchaseRequestCategory;

/**
 * ProjectPrice
 *
 * @ORM\Table(name="project_price")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectPriceRepository")
 */
class ProjectPrice
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
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="PriceIteration", mappedBy="projectPrice", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $iterations;

    /**
     * ProjectPrice constructor.
     */
    public function __construct()
    {
        $this->iterations = new ArrayCollection();
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
     * Set project.
     *
     * @param Project $project
     *
     * @return ProjectPrice
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project.
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return mixed
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * @param mixed $iterations
     * @return ProjectPrice
     */
    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
        return $this;
    }

    /**
     * @return array
     */
    public function getPriceCategories()
    {
        $priceCategories = [];
        foreach ($this->iterations as $iteration) {
            foreach ($iteration->getCategoryPrices() as $categoryPrice) {
                $priceCategories[$categoryPrice->getCategory()->getTitle()][] = $categoryPrice;
            }
        }
        return $priceCategories;
    }

    public function getCategories()
    {
        $categories = [];

        foreach ($this->iterations as $iteration) {
            foreach ($iteration->getCategoryPrices() as $categoryPrice) {
                if (!in_array($categoryPrice->getCategory(), $categories)) {
                    $categories[] = $categoryPrice->getCategory();
                }
            }
        }

        return $categories;
    }
}
