<?php


namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Repository\WarehouseRepository;

/**
 * Warehouse
 *
 * @ORM\Table(name="warehouse")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\WarehouseRepository")
 */
class Warehouse
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="short_title", type="string", length=255)
     */
    private $shortTitle = '';

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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Warehouse
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get shortTitle
     *
     * @return string
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
    }

    /**
     * Set shortTitle
     *
     * @param string $shortTitle
     *
     * @return Warehouse
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }

}