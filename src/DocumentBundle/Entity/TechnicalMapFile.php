<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * TechnicalMapFile
 *
 * @ORM\Table(name="technical_map_file")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\TechnicalMapFileRepository")
 */
class TechnicalMapFile
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="file_size", type="string", length=255)
     */
    private $fileSize;

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
     * @ORM\ManyToOne (targetEntity="DocumentBundle\Entity\TechnicalMap")
     * @ORM\JoinColumn(name="technical_map_id", referencedColumnName="id")
     */
    private $technicalMap;

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
     * Set name
     *
     * @param string $fileSize
     *
     * @return $this
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
     * Set format
     *
     * @param string $format
     *
     * @return TechnicalMapFile
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
     * @return TechnicalMapFile
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
     * @param TechnicalMap $technicalMap
     *
     * @return TechnicalMapFile
     */
    public function setTechnicalMap($technicalMap)
    {
        $this->technicalMap = $technicalMap;

        return $this;
    }

    /**
     * @return TechnicalMap
     */
    public function getTechnicalMap()
    {
        return $this->technicalMap;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return TechnicalMapFile
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
     * Set fileName
     *
     * @param string $fileName
     *
     * @return TechnicalMapFile
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
    public function canDeleteFile(User $user)
    {
        return $this->getOwner()->getId() == $user->getId();
    }
}