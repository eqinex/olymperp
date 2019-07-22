<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interview
 *
 * @ORM\Table(name="interview")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterviewRepository")
 */
class Interview
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vacancy")
     * @ORM\JoinColumn(name="vacancy_id", referencedColumnName="id")
     */
    private $vacancy;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Applicant")
     * @ORM\JoinColumn(name="applicant_id", referencedColumnName="id")
     */
    private $applicant;

    /**
     * @var string
     *
     * @ORM\Column(name="notice", type="text", nullable=true)
     */
    private $notice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="start_at", nullable=true)
     */
    private $startAt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

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
     * Set vacancy
     *
     * @param Vacancy $vacancy
     *
     * @return $this
     */
    public function setVacancy($vacancy)
    {
        $this->vacancy = $vacancy;

        return $this;
    }

    /**
     * Get vacancy
     *
     * @return vacancy
     */
    public function getVacancy()
    {
        return $this->vacancy;
    }

    /**
     * Set applicant
     *
     * @param Applicant $applicant
     *
     * @return $this
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;

        return $this;
    }

    /**
     * Get applicant
     *
     * @return applicant
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * Set notice
     *
     * @param string $notice
     *
     * @return $this
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
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
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return $this
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}

