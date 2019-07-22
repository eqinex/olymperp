<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * DocumentRevision
 *
 * @ORM\Table(name="document_revision", indexes={@Index(name="IDX_previous_revision_id", columns={"previous_revision_id"})})
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\DocumentRevisionRepository")
 */
class DocumentRevision
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
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="version", type="integer")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\DocumentRevision")
     * @ORM\JoinColumn(name="previous_revision_id", referencedColumnName="id")
     *
     */
    private $previousRevision;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

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
     * Set document
     *
     * @param \stdClass $document
     *
     * @return DocumentRevision
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \stdClass
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return DocumentRevision
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set version
     *
     * @param integer $version
     *
     * @return DocumentRevision
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getPreviousRevision()
    {
        return $this->previousRevision;
    }

    /**
     * @param mixed $previousRevision
     */
    public function setPreviousRevision($previousRevision)
    {
        $this->previousRevision = $previousRevision;
    }

    /**
     * Set owner
     *
     * @param \stdClass $owner
     *
     * @return DocumentRevision
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \stdClass
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return DocumentRevision
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

