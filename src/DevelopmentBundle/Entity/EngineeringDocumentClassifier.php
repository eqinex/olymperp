<?php

namespace DevelopmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentCategory
 *
 * @ORM\Table(name="engineering_document_classifier")
 * @ORM\Entity(repositoryClass="DevelopmentBundle\Repository\EngineeringDocumentClassifierRepository")
 */
class EngineeringDocumentClassifier
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
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="subgroup", type="string", length=255, nullable=true)
     */
    private $subgroup;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * Set class
     *
     * @param string $class
     *
     * @return EngineeringDocumentClassifier
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set subgroup
     *
     * @param string $subgroup
     *
     * @return EngineeringDocumentClassifier
     */
    public function setSubgroup($subgroup)
    {
        $this->subgroup = $subgroup;

        return $this;
    }

    /**
     * Get $subgroup
     *
     * @return string
     */
    public function getSubgroup()
    {
        return $this->subgroup;
    }

    /**
     * @param string $description
     * @return EngineeringDocumentClassifier
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}