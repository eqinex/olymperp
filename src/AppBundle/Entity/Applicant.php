<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * Applicant
 *
 * @ORM\Table(name="applicant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApplicantRepository")
 */
class Applicant
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
     * @ORM\Column(name="firstname", type="text")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="text")
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="text", nullable=true)
     */
    protected $middleName;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApplicantStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="notice", type="text", nullable=true)
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", length=180)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="text", nullable=true)
     */
    protected $phone;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectRole")
     * @ORM\JoinColumn(name="employee_role_id", referencedColumnName="id")
     */
    private $employeeRole;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="ApplicantFile", mappedBy="applicant", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $attachments;

    /**
     * Applicant constructor.
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
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
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get status
     *
     * @return ApplicantStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param ApplicantStatus $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get employeeRole
     *
     * @return ProjectRole
     */
    public function getEmployeeRole()
    {
        return $this->employeeRole;
    }

    /**
     * Set employeeRole
     *
     * @param ProjectRole $employeeRole
     *
     * @return $this
     */
    public function setEmployeeRole($employeeRole)
    {
        $this->employeeRole = $employeeRole;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param /DateTime $createdAt
     *
     * @return Applicant
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return /DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param mixed $attachments
     * @return Applicant
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }
}