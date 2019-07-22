<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * TechnicalMapComment
 *
 * @ORM\Table(name="technical_map_comment")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\TechnicalMapCommentRepository")
 */
class TechnicalMapComment
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
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\TechnicalMap")
     * @ORM\JoinColumn(name="technical_map_id", referencedColumnName="id")
     */
    private $technicalMap;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\TechnicalMapComment")
     * @ORM\JoinColumn(name="parent_comment_id", referencedColumnName="id")
     */
    private $parentComment;

    /**
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\TechnicalMapComment", mappedBy="parentComment", cascade="all")
     */
    private $replies;

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
     * @return TechnicalMapComment
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
     * @return TechnicalMapComment
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
     * @return TechnicalMapComment
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
     * @param TechnicalMap $technicalMap
     *
     * @return TechnicalMapComment
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
     * @return mixed
     */
    public function getParentComment()
    {
        return $this->parentComment;
    }

    /**
     * @param mixed $parentComment
     * @return TechnicalMapComment
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
     * @return TechnicalMapComment
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
        return $this;
    }
}