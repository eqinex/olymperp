<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Book
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(name="editor", type="string", length=255, nullable=true)
     */
    protected $editor;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column( type="string", length=255, name="year_of_issue")
     */
    private $yearOfIssue;

    /**
     * @var string
     *
     * @ORM\Column(name="publishing_house",  type="string", length=255, nullable=true)
     */
    protected $publishingHouse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="paper_version",  type="boolean")
     */
    protected $paperVersion = false;

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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get editor
     *
     * @return string
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set editor
     *
     * @param string $editor
     *
     * @return $this
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get yearOfIssue
     *
     * @return string
     */
    public function getYearOfIssue()
    {
        return $this->yearOfIssue;
    }

    /**
     * Set yearOfIssue
     *
     * @param string $yearOfIssue
     *
     * @return $this
     */
    public function setYearOfIssue($yearOfIssue)
    {
        $this->yearOfIssue = $yearOfIssue;

        return $this;
    }

    /**
     * Get publishingHouse
     *
     * @return string
     */
    public function getPublishingHouse()
    {
        return $this->publishingHouse;
    }

    /**
     * Set publishingHouse
     *
     * @param string $publishingHouse
     *
     * @return $this
     */
    public function setPublishingHouse($publishingHouse)
    {
        $this->publishingHouse = $publishingHouse;

        return $this;
    }

    /**
     * Get paperVersion
     *
     * @return boolean
     */
    public function getPaperVersion()
    {
        return $this->paperVersion;
    }

    /**
     * Set paperVersion
     *
     * @param boolean $paperVersion
     *
     * @return $this
     */
    public function setPaperVersion($paperVersion)
    {
        $this->paperVersion = $paperVersion;

        return $this;
    }
}