<?php


namespace DevelopmentBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectCode
 *
 * @ORM\Table(name="project_code")
 * @ORM\Entity(repositoryClass="DevelopmentBundle\Repository\ProjectCodeRepository")
 */
class ProjectCode
{
    const STAGE_PRE_ACTIVITIES = 0;
    const STAGE_PRELIMINARY_DESIGN = 1;
    const STAGE_TECHNICAL_PROJECT = 2;
    const STAGE_UNIT_PRODUCTION = 3;
    const STAGE_PROTOTYPE = 4;
    const STAGE_MASS_PRODUCTION = 5;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DevelopmentBundle\Entity\CompanyCode")
     * @ORM\JoinColumn(name="company_code_id", referencedColumnName="id")
     */
    private $companyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="project_number", type="string")
     */
    private $projectNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="project_stage", type="string", nullable=true)
     */
    private $projectStage;

    /**
     * @var string
     *
     * @ORM\Column(name="created_year", type="string", nullable=true)
     */
    private $createdYear;

    /**
     * @var string
     *
     * @ORM\Column(name="subassembly", type="string", nullable=true)
     */
    private $subassembly;

    /**
     * @var string
     *
     * @ORM\Column(name="execution", type="string", nullable=true)
     */
    private $execution;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id", nullable=true)
     */
    private $responsible;

    /**
     * @var string
     *
     * @ORM\Column(name="reserve_responsible", type="string", nullable=true)
     */
    private $reserveResponsible;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_registration", type="datetime", nullable=true)
     */
    private $dateOfRegistration;

    /**
     * @var string
     *
     * @ORM\Column(name="inside_code", type="string", nullable=true)
     */
    private $insideCode;

    /**
     * @var string
     *
     * @ORM\Column(name="project_location", type="string", nullable=true)
     */
    private $projectLocation;

     /**
     * @var string
     *
     * @ORM\Column(name="kit_engineering_document", type="string", nullable=true)
     */
    private $kitEngineeringDocument;

    /**
     * @var string
     *
     * @ORM\Column(name="project_structure", type="string", nullable=true)
     */
    private $projectStructure;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", nullable=true)
     */
    private $remark;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get companyCode
     *
     * @return mixed
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * Set companyCode
     *
     * @param CompanyCode $companyCode
     *
     * @return $this
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;

        return $this;
    }

    /**
     * Get projectStage
     *
     * @return string
     */
    public function getProjectStage()
    {
        return $this->projectStage;
    }

    /**
     * Set projectStage
     *
     * @param $projectStage
     *
     * @return $this
     */
    public function setProjectStage($projectStage)
    {
        $this->projectStage = $projectStage;

        return $this;
    }

    /**
     * Get responsible
     *
     * @return mixed
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Set responsible
     *
     * @param User $responsible
     *
     * @return $this
     */
    public function setResponsible($responsible)
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Get reserveResponsible
     *
     * @return string
     */
    public function getReserveResponsible()
    {
        return $this->reserveResponsible;
    }

    /**
     * Set reserveResponsible
     *
     * @param $reserveResponsible
     *
     * @return $this
     */
    public function setReserveResponsible($reserveResponsible)
    {
        $this->reserveResponsible = $reserveResponsible;

        return $this;
    }

    /**
     * Get subassembly
     *
     * @return string
     */
    public function getSubassembly()
    {
        return $this->subassembly;
    }

    /**
     * Set subassembly
     *
     * @param $subassembly
     *
     * @return $this
     */
    public function setSubassembly($subassembly)
    {
        $this->subassembly = $subassembly;

        return $this;
    }

    /**
     * Get execution
     *
     * @return string
     */
    public function getExecution()
    {
        return $this->execution;
    }

    /**
     * Set execution
     *
     * @param $execution
     *
     * @return $this
     */
    public function setExecution($execution)
    {
        $this->execution = $execution;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get insideCode
     *
     * @return string
     */
    public function getInsideCode()
    {
        return $this->insideCode;
    }

    /**
     * Set insideCode
     *
     * @param $insideCode
     *
     * @return $this
     */
    public function setInsideCode($insideCode)
    {
        $this->insideCode = $insideCode;

        return $this;
    }

    /**
     * Get projectLocation
     *
     * @return string
     */
    public function getProjectLocation()
    {
        return $this->projectLocation;
    }

    /**
     * Set projectLocation
     *
     * @param $projectLocation
     *
     * @return $this
     */
    public function setProjectLocation($projectLocation)
    {
        $this->projectLocation = $projectLocation;

        return $this;
    }

    /**
     * Get createdYear
     *
     * @return string
     */
    public function getCreatedYear()
    {
        return $this->createdYear;
    }

    /**
     * Set createdYear
     *
     * @param $createdYear
     *
     * @return $this
     */
    public function setCreatedYear($createdYear)
    {
        $this->createdYear = $createdYear;

        return $this;
    }

    /**
     * Get kitEngineeringDocument
     *
     * @return string
     */
    public function getKitEngineeringDocument()
    {
        return $this->kitEngineeringDocument;
    }

    /**
     * Set kitEngineeringDocument
     *
     * @param $kitEngineeringDocument
     *
     * @return $this
     */
    public function setKitEngineeringDocument($kitEngineeringDocument)
    {
        $this->kitEngineeringDocument = $kitEngineeringDocument;

        return $this;
    }

    /**
     * Get projectStructure
     *
     * @return string
     */
    public function getProjectStructure()
    {
        return $this->projectStructure;
    }

    /**
     * Set projectStructure
     *
     * @param $projectStructure
     *
     * @return $this
     */
    public function setProjectStructure($projectStructure)
    {
        $this->projectStructure = $projectStructure;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set remark
     *
     * @param $remark
     *
     * @return $this
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get ProjectNumber
     *
     * @return string
     */
    public function getProjectNumber()
    {
        return $this->projectNumber;
    }

    /**
     * Set projectNumber
     *
     * @param $projectNumber
     *
     * @return $this
     */
    public function setProjectNumber($projectNumber)
    {
        $this->projectNumber = $projectNumber;

        return $this;
    }

    /**
     * Is deleted
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted
     *
     * @param bool $deleted
     *
     * @return ProjectCode
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get dateOfRegistration
     *
     * @return \DateTime
     */
    public function getDateOfRegistration()
    {
        return $this->dateOfRegistration;
    }

    /**
     * Set dateOfRegistration
     *
     * @param \DateTime $dateOfRegistration
     *
     * @return ProjectCode
     */
    public function setDateOfRegistration($dateOfRegistration)
    {
        $this->dateOfRegistration = $dateOfRegistration;

        return $this;
    }

    /**
     * Get stageList
     *
     * @return array
     */
    public static function getStageList()
    {
        return [
            self::STAGE_PRE_ACTIVITIES,
            self::STAGE_PRELIMINARY_DESIGN,
            self::STAGE_TECHNICAL_PROJECT,
            self::STAGE_UNIT_PRODUCTION,
            self::STAGE_PROTOTYPE,
            self::STAGE_MASS_PRODUCTION
        ];
    }
}