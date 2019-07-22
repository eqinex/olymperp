<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseRequestFile
 *
 * @ORM\Table(name="purchase_request_file")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\RequestFileRepository")
 */
class RequestFile
{
    const FILE_TYPE_DEFAULT = 'default';
    const FILE_TYPE_FINANCIAL = 'financial';
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
     * @ORM\Column(name="file_size", type="string", length=255)
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = 'default';

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=255)
     */
    private $format;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploaded_at", type="datetime")
     */
    private $uploadedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseRequest")
     * @ORM\JoinColumn(name="purchase_request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="text")
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="stored_file_name", type="text")
     */
    private $storedFileName;

    /**
     * @var string
     *
     * @ORM\Column(name="stored_preview_file_name", type="text", nullable=true)
     */
    private $storedPreviewFileName;

    /**
     * @var string
     *
     * @ORM\Column(name="stored_file_dir", type="text", nullable=true)
     */
    private $storedFileDir;

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
     * @param string $fileSize
     *
     * @return RequestFile
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return RequestFile
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set format
     *
     * @param string $format
     *
     * @return RequestFile
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
     * Set uploadedAt
     *
     * @param \DateTime $uploadedAt
     *
     * @return RequestFile
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get uploadedAt
     *
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return RequestFile
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
     * @param PurchaseRequest $purchaseRequest
     *
     * @return RequestFile
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * Get project
     *
     * @return PurchaseRequest
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return RequestFile
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return RequestFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getStoredFileName()
    {
        return $this->storedFileName ?: $this->fileName;
    }

    /**
     * @param string $storedFileName
     * @return $this
     */
    public function setStoredFileName($storedFileName)
    {
        $this->storedFileName = $storedFileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoredPreviewFileName()
    {
        return $this->storedPreviewFileName ?: $this->fileName;
    }

    /**
     * @param string $storedPreviewFileName
     * @return $this
     */
    public function setStoredPreviewFileName($storedPreviewFileName)
    {
        $this->storedPreviewFileName = $storedPreviewFileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoredFileDir()
    {
        return $this->storedFileDir;
    }

    /**
     * @param string $storedFileDir
     * @return $this
     */
    public function setStoredFileDir($storedFileDir)
    {
        $this->storedFileDir = $storedFileDir;
        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canViewFile(User $user)
    {
        return !$this->getType() || $this->getType() == 'default' ||
            ($user->canViewFinancialInfo() && in_array($this->getType(), [self::FILE_TYPE_FINANCIAL]));
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canDeleteFile(User $user)
    {
        return $this->getOwner()->getId() == $user->getId() && (
            ($this->getType() == RequestFile::FILE_TYPE_DEFAULT &&
                $this->getPurchaseRequest()->canEditItems($user)) ||
            ($this->getType() == RequestFile::FILE_TYPE_FINANCIAL &&
                $this->getPurchaseRequest()->canManagerFinishWork($user))
        );
    }

    /**
     * @return bool
     */
    public function isFileOutdated()
    {
        return $this->getUploadedAt()->diff(new \DateTime('now'))->days > 7;
    }
}

