<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectFile
 *
 * @ORM\Table(name="project_file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"project_file" = "ProjectFile", "task_file" = "TaskFile" })
 */
abstract class File
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

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
     * @var bool
     *
     * @ORM\Column(name="full_access", type="boolean")
     */
    private $fullAccess = false;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="acl_file",
     *      joinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectPassport")
     * @ORM\JoinColumn(name="project_passport_id", referencedColumnName="id", nullable=true)
     */
    private $projectPassport;

    public function __construct()
    {
        $this->fullAccess = false;
        $this->users = new ArrayCollection();
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
     * @return $this
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
     * @return $this
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
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->owner->getId() == $user->getId();
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return $this
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
     * @return $this
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
     * @return File
     */
    public function setStoredFileDir($storedFileDir)
    {
        $this->storedFileDir = $storedFileDir;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFullAccess()
    {
        return $this->fullAccess;
    }

    /**
     * @param boolean $fullAccess
     * @return File
     */
    public function setFullAccess($fullAccess)
    {
        $this->fullAccess = $fullAccess;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * Set projectPassport
     *
     * @param ProjectPassport $projectPassport
     *
     * @return $this
     */
    public function setProjectPassport($projectPassport)
    {
        $this->projectPassport = $projectPassport;

        return $this;
    }

    /**
     * Get projectPassport
     *
     * @return ProjectPassport
     */
    public function getProjectPassport()
    {
        return $this->projectPassport;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser($user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser($user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canDeleteFile(User $user)
    {
        return $this->getOwner()->getId() == $user->getId() || $user->canDeleteProjectFiles();
    }

    /**
     * @return array
     */
    public function getAccessUsersIds()
    {
        $ids = [];

        foreach ($this->getUsers() as $user) {
            $ids[] = $user->getId();
        }

        return $ids;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasAccess(User $user)
    {
        return $this->getOwner()->getId() == $user->getId() ||
            $this->isFullAccess() ||
            $this->users->contains($user) ||
            $user->hasAccessFiles();
    }
}

