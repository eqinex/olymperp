<?php

namespace DevelopmentBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProgrammingDocument
 *
 * @ORM\Table(name="programming_document")
 * @ORM\Entity(repositoryClass="DevelopmentBundle\Repository\ProgrammingDocumentRepository")
 */
class ProgrammingDocument
{
    const CODE_VMI = 'vmi';
    const CODE_BPAG = 'bpag';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="DevelopmentBundle\Entity\ProgrammingDocumentType")
     * @ORM\JoinColumn(name="programming_document_type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="number_of_pages", type="integer", nullable=true)
     */
    private $numberOfPages;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="edition_number", type="string", length=255, nullable=true)
     */
    private $editionNumber;

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
     * @ORM\OneToOne (targetEntity="ProgrammingDocumentFile")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="document_number", type="integer")
     */
    private $documentNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="register_number", type="integer")
     */
    private $registerNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="inventory_number", type="string", length=255, nullable=true)
     */
    private $inventoryNumber;

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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return ProgrammingDocument
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param ProgrammingDocumentType $type
     * @return ProgrammingDocument
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $designation
     * @return ProgrammingDocument
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $numberOfPages
     * @return ProgrammingDocument
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProgrammingDocument
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
     * Set editionNumber
     *
     * @param string $editionNumber
     *
     * @return ProgrammingDocument
     */
    public function setEditionNumber($editionNumber)
    {
        $this->editionNumber = $editionNumber;

        return $this;
    }

    /**
     * Get editionNumber
     *
     * @return string
     */
    public function getEditionNumber()
    {
        return $this->editionNumber;
    }

    /**
     * Set notice
     *
     * @param string $notice
     *
     * @return ProgrammingDocument
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
     * @param ProgrammingDocumentFile $file
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
     * @return ProgrammingDocumentFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set documentNumber
     *
     * @param integer $documentNumber
     *
     * @return ProgrammingDocument
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * Set registerNumber
     *
     * @param integer $registerNumber
     *
     * @return ProgrammingDocument
     */
    public function setRegisterNumber($registerNumber)
    {
        $this->registerNumber = $registerNumber;

        return $this;
    }

    /**
     * @return integer
     */
    public function getRegisterNumber()
    {
        return $this->registerNumber;
    }

    /**
     * Set inventoryNumber
     *
     * @param string $inventoryNumber
     *
     * @return ProgrammingDocument
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
     * @return array
     */
    public static function getCodeList()
    {
        return [
            self::CODE_VMI => self::CODE_VMI,
            self::CODE_BPAG => self::CODE_BPAG,
        ];
    }
}