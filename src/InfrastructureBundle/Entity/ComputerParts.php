<?php
/**
 * Created by PhpStorm.
 * User: shemyakinDV
 * Date: 24.02.2019
 * Time: 15:37
 */

namespace InfrastructureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Computer
 *
 * @ORM\Table(name="computer_parts")
 * @ORM\Entity(repositoryClass="InfrastructureBundle\Repository\ComputerPartsRepository")
 */
class ComputerParts
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
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\Computer")
     * @ORM\JoinColumn(name="computer_id", referencedColumnName="id")
     */
    private $computer;

    /**
     * @ORM\ManyToOne(targetEntity="InfrastructureBundle\Entity\ComputerPart")
     * @ORM\JoinColumn(name="part_id", referencedColumnName="id")
     */
    private $part;

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
     * Set computer
     *
     * @param \stdClass $computer
     *
     * @return ComputerParts
     */
    public function setComputer($computer)
    {
        $this->computer = $computer;

        return $this;
    }

    /**
     * Get computer
     *
     * @return \stdClass
     */
    public function getComputer()
    {
        return $this->computer;
    }

    /**
     * Set part
     *
     * @param \stdClass $part
     *
     * @return ComputerParts
     */
    public function setPart($part)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * Get part
     *
     * @return \stdClass
     */
    public function getPart()
    {
        return $this->part;
    }
}