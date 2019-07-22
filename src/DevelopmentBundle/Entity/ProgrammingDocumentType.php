<?php

namespace DevelopmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentCategory
 *
 * @ORM\Table(name="programming_document_type")
 * @ORM\Entity(repositoryClass="DevelopmentBundle\Repository\ProgrammingDocumentTypeRepository")
 */
class ProgrammingDocumentType
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

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=60, nullable=true)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_number", type="integer", options={"default":0})
     */
    private $lastNumber;

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
     * @return ProgrammingDocumentType
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
     * Set code
     *
     * @param string $code
     *
     * @return ProgrammingDocumentType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $lastNumber
     * @return ProgrammingDocumentType
     */
    public function setLastNumber($lastNumber)
    {
        $this->lastNumber = $lastNumber;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLastNumber()
    {
        return $this->lastNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}