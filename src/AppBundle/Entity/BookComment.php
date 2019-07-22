<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookComment
 *
 * @ORM\Table(name="book_comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookCommentRepository")
 */
class BookComment
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Book")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookComment")
     * @ORM\JoinColumn(name="parent_comment_id", referencedColumnName="id")
     */
    private $parentComment;

    /**
     * @ORM\OneToMany(targetEntity="BookComment", mappedBy="parentComment", cascade="all")
     */
    private $replies;


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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return $this
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
     * @return $this
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
     * Set book
     *
     * @param Book $book
     *
     * @return $this
     */
    public function setBook($book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
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
     * @return $this
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
     * @return $this
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
        return $this;
    }
}