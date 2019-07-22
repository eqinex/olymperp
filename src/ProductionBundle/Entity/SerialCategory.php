<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.02.19
 * Time: 9:18
 */

namespace ProductionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Serial
 *
 * @ORM\Table(name="serial_category")
 * @ORM\Entity(repositoryClass="ProductionBundle\Repository\SerialCategoryRepository")
 */
class SerialCategory
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name = '';

    public function __construct()
    {
        $this->name = '';
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
     * Set name
     *
     * @param string $name
     *
     * @return SerialCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}