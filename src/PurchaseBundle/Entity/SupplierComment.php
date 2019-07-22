<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierComment
 *
 * @ORM\Table(name="supplier_comment")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\SupplierCommentRepository")
 */
class SupplierComment
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_text", type="text")
     */
    private $commentText;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\SupplierComment")
     * @ORM\JoinColumn(name="parent_comment_id", referencedColumnName="id")
     */
    private $parentComment;

    /**
     * @ORM\OneToMany(targetEntity="SupplierComment", mappedBy="parentComment", cascade="all")
     */
    private $replies;

    /**
     * SupplierComment constructor.
     */
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
     * Set owner
     *
     * @param User $owner
     *
     * @return SupplierComment
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SupplierComment
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

    /**
     * Set commentText
     *
     * @param string $commentText
     *
     * @return SupplierComment
     */
    public function setCommentText($commentText)
    {
        $this->commentText = $commentText;

        return $this;
    }

    /**
     * Get commentText
     *
     * @return string
     */
    public function getCommentText()
    {
        return $this->commentText;
    }

    /**
     * Set supplier
     *
     * @param Supplier $supplier
     *
     * @return SupplierComment
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @return mixed
     */
    public function getParentComment()
    {
        return $this->parentComment;
    }

    /**
     * @param mixed $parentComment
     * @return SupplierComment
     */
    public function setParentComment($parentComment)
    {
        $this->parentComment = $parentComment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param mixed $replies
     * @return SupplierComment
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
        return $this;
    }
}