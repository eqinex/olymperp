<?php

namespace DevelopmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;

/**
 * EngineeringDocument
 *
 * @ORM\Table(name="engineering_document",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="engineering_document_unique",
 *            columns={"code", "classifier_code", "index_number", "document_execution"})
 *    }
 * )
 *
 * @ORM\Entity(repositoryClass="DevelopmentBundle\Repository\EngineeringDocumentRepository")
 */
class EngineeringDocument
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
     * @ORM\Column(name="inventory_number", type="string", length=255)
     */
    private $inventoryNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="type_of_document", type="string", length=255)
     */
    private $typeOfDocument;

    /**
     * @var string
     *
     * @ORM\Column(name="number_of_pages", type="integer", nullable=true)
     */
    private $numberOfPages;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=255, nullable=true)
     */
    private $format;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=155)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="classifier_code", type="string", length=255)
     */
    private $classifierCode;

    /**
     * @var string
     *
     * @ORM\Column(name="index_number", type="string", length=155)
     */
    private $indexNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="document_execution", type="string", length=155, nullable=true)
     */
    private $documentExecution;

    /**
     * @var string
     *
     * @ORM\Column(name="decryption_code", type="text", nullable=true)
     */
    private $decryptionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="notice", type="text", nullable=true)
     */
    private $notice;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\OneToOne (targetEntity="EngineeringDocumentFile")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set inventoryNumber
     *
     * @param string $inventoryNumber
     *
     * @return EngineeringDocument
     */
    public function setInventoryNumber($inventoryNumber)
    {
        $this->inventoryNumber = $inventoryNumber;

        return $this;
    }

    /**
     * Get inventoryNumber
     *
     * @return string
     */
    public function getInventoryNumber()
    {
        return $this->inventoryNumber;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return EngineeringDocument
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return EngineeringDocument
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get inventoryNumber
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set typeOfDocument
     *
     * @param string $typeOfDocument
     *
     * @return EngineeringDocument
     */
    public function setTypeOfDocument($typeOfDocument)
    {
        $this->typeOfDocument = $typeOfDocument;

        return $this;
    }

    /**
     * Get inventoryNumber
     *
     * @return string
     */
    public function getTypeOfDocument()
    {
        return $this->typeOfDocument;
    }

    /**
     * Set numberOfPages
     *
     * @param string $numberOfPages
     *
     * @return EngineeringDocument
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    /**
     * Get numberOfPages
     *
     * @return string
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return EngineeringDocument
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return EngineeringDocument
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
     * Set code
     *
     * @param string $code
     *
     * @return EngineeringDocument
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
     * Set classifierCode
     *
     * @param string $classifierCode
     *
     * @return EngineeringDocument
     */
    public function setClassifierCode($classifierCode)
    {
        $this->classifierCode = $classifierCode;

        return $this;
    }

    /**
     * Get classifierCode
     *
     * @return string
     */
    public function getClassifierCode()
    {
        return $this->classifierCode;
    }

    /**
     * Set indexNumber
     *
     * @param string $indexNumber
     *
     * @return EngineeringDocument
     */
    public function setIndexNumber($indexNumber)
    {
        $this->indexNumber = $indexNumber;

        return $this;
    }

    /**
     * Get indexNumber
     *
     * @return string
     */
    public function getIndexNumber()
    {
        return $this->indexNumber;
    }

    /**
     * Set documentExecution
     *
     * @param string $documentExecution
     *
     * @return EngineeringDocument
     */
    public function setDocumentExecution($documentExecution)
    {
        $this->documentExecution = $documentExecution;

        return $this;
    }

    /**
     * Get documentExecution
     *
     * @return string
     */
    public function getDocumentExecution()
    {
        return $this->documentExecution;
    }

    /**
     * Set decryptionCode
     *
     * @param string $decryptionCode
     *
     * @return EngineeringDocument
     */
    public function setDecryptionCode($decryptionCode)
    {
        $this->decryptionCode = $decryptionCode;

        return $this;
    }

    /**
     * Get decryptionCode
     *
     * @return string
     */
    public function getDecryptionCode()
    {
        return $this->decryptionCode;
    }

    /**
     * Set notice
     *
     * @param string $notice
     *
     * @return EngineeringDocument
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set file
     *
     * @param EngineeringDocumentFile $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return EngineeringDocumentFile
     */
    public function getFile()
    {
        return $this->file;
    }
}