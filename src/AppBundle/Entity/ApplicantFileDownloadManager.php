<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicantFileDownloadManager
 *
 * @ORM\Table(name="applicant_file_download_manager")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApplicantFileDownloadManagerRepository")
 */
class ApplicantFileDownloadManager
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApplicantFile")
     * @ORM\JoinColumn(name="applicant_file_id", referencedColumnName="id")
     */
    private $applicantFile;

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
     * @return ApplicantFile
     */
    public function getApplicantFile()
    {
        return $this->applicantFile;
    }

    /**
     * @param $applicantFile
     * @return $this
     */
    public function setApplicantFile($applicantFile)
    {
        $this->applicantFile = $applicantFile;
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
