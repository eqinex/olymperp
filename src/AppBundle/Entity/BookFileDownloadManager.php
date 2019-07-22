<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookFileDownloadManager
 *
 * @ORM\Table(name="book_file_download_manager")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookFileDownloadManagerRepository")
 */
class BookFileDownloadManager
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookFile")
     * @ORM\JoinColumn(name="book_file_id", referencedColumnName="id")
     */
    private $bookFile;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="download_date", type="datetime")
     */
    private $downloadDate;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BookFile
     */
    public function getBookFile()
    {
        return $this->bookFile;
    }

    /**
     * @param $bookFile
     * @return $this
     */
    public function setBookFile($bookFile)
    {
        $this->bookFile = $bookFile;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param \DateTime $downloadDate
     * @return $this
     */
    public function setDownloadDate($downloadDate)
    {
        $this->downloadDate = $downloadDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDownloadDate()
    {
        return $this->downloadDate;
    }
}
