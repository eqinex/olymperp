<?php

namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Client;

/**
 * Supplier
 *
 * @ORM\Table(name="supplier", indexes={@ORM\Index(name="idx_client_id", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\SupplierRepository")
 */
class Supplier
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="full_title", type="string", length=255, nullable=true)
     */
    private $fullTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="one_s_unique_code", type="string", length=255, nullable=true, unique=true)
     */
    private $oneSUniqueCode;

    /**
     * Supplies category.
     * @ORM\ManyToMany(targetEntity="PurchaseBundle\Entity\SuppliesCategory")
     * @ORM\JoinTable(name="supplier_supplies_category",
     *      joinColumns={@ORM\JoinColumn(name="supplier_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", unique=false)}
     *      )
     */
    private $supplierCategories;

    /**
     * @var string
     *
     * @ORM\Column(name="legal_address", type="string", length=255, nullable=true)
     */
    private $legalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="actual_address", type="string", length=255, nullable=true)
     */
    private $actualAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_address", type="string", length=255, nullable=true)
     */
    private $postalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=true)
     */
    private $ogrn;

    /**
     * @var string
     *
     * @ORM\Column(name="itn", type="string", length=255, unique=true, nullable=true)
     */
    private $itn;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="okpo", type="string", length=255, nullable=true)
     */
    private $okpo;

    /**
     * @var string
     *
     * @ORM\Column(name="okved", type="string", length=255, nullable=true)
     */
    private $okved;

    /**
     * @var string
     *
     * @ORM\Column(name="okfs", type="string", length=255, nullable=true)
     */
    private $okfs;

    /**
     * @var string
     *
     * @ORM\Column(name="okopf", type="string", length=255, nullable=true)
     */
    private $okopf;

    /**
     * @var string
     *
     * @ORM\Column(name="okato", type="string", length=255, nullable=true)
     */
    private $okato;

    /**
     * @var string
     *
     * @ORM\Column(name="director", type="string", length=255, nullable=true)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="basis", type="string", length=255, nullable=true)
     */
    private $basis;

    /**
     * @var string
     *
     * @ORM\Column(name="accountant", type="string", length=255, nullable=true)
     */
    private $accountant;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="registeredAt", nullable=true)
     */
    private $registeredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="checking_account", type="string", length=255, nullable=true)
     */
    private $checkingAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_short_name", type="string", length=255, nullable=true)
     */
    private $bankShortName;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_full_name", type="string", length=255, nullable=true)
     */
    private $bankFullName;

    /**
     * @var string
     *
     * @ORM\Column(name="correspondent_account", type="string", length=255, nullable=true)
     */
    private $correspondentAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255, nullable=true)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_mailing_address", type="string", length=255, nullable=true)
     */
    private $bankMailingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_legal_address", type="string", length=255, nullable=true)
     */
    private $bankLegalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_actual_address", type="string", length=255, nullable=true)
     */
    private $bankActualAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_itn", type="string", length=255, nullable=true)
     */
    private $bankItn;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_kpp", type="string", length=255, nullable=true)
     */
    private $bankKpp;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\Document", mappedBy="supplier", cascade="all")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\SupplierLegalForm")
     * @ORM\JoinColumn(name="legal_form_id", referencedColumnName="id")
     */
    private $legalForm;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="added_to_blacklist", type="boolean", nullable=true)
     */
    private $addedToBlackList = 0;

    /**
     * @ORM\OneToMany(targetEntity="SupplierIncident", mappedBy="supplier", cascade="all")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $incidents;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->title = '';
        $this->supplierCategories = new ArrayCollection();
        $this->incidents = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Supplier
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     * Set fullTitle
     *
     * @param string $fullTitle
     *
     * @return Supplier
     */
    public function setFullTitle($fullTitle)
    {
        $this->fullTitle = $fullTitle;

        return $this;
    }

    /**
     * Get fullTitle
     *
     * @return string
     */
    public function getFullTitle()
    {
        return $this->fullTitle;
    }

    /**
     * Set oneSUniqueCode
     *
     * @param string $oneSUniqueCode
     *
     * @return Supplier
     */
    public function setOneSUniqueCode($oneSUniqueCode)
    {
        $this->oneSUniqueCode = $oneSUniqueCode;

        return $this;
    }

    /**
     * Get oneSUniqueCode
     *
     * @return string
     */
    public function getOneSUniqueCode()
    {
        return $this->oneSUniqueCode;
    }

    /**
     * @return ArrayCollection
     */
    public function getSupplierCategories()
    {
        return $this->supplierCategories;
    }

    /**
     * @param mixed $supplierCategories
     * @return Supplier
     */
    public function setSupplierCategories($supplierCategories)
    {
        $this->supplierCategories = $supplierCategories;
        return $this;
    }

    /**
     * Set legalAddress
     *
     * @param string $legalAddress
     *
     * @return Supplier
     */
    public function setLegalAddress($legalAddress)
    {
        $this->legalAddress = $legalAddress;

        return $this;
    }

    /**
     * Get legalAddress
     *
     * @return string
     */
    public function getLegalAddress()
    {
        return $this->legalAddress;
    }

    /**
     * Set actualAddress
     *
     * @param string $actualAddress
     *
     * @return Supplier
     */
    public function setActualAddress($actualAddress)
    {
        $this->actualAddress = $actualAddress;

        return $this;
    }

    /**
     * Get postalAddress
     *
     * @return string
     */
    public function getPostalAddress()
    {
        return $this->postalAddress;
    }

    /**
     * Set postalAddress
     *
     * @param string $postalAddress
     *
     * @return Supplier
     */
    public function setPostalAddress($postalAddress)
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }

    /**
     * Get actualAddress
     *
     * @return string
     */
    public function getActualAddress()
    {
        return $this->actualAddress;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Supplier
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * Set site
     *
     * @param string $site
     *
     * @return Supplier
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Supplier
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

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
     * Set fax
     *
     * @param string $fax
     *
     * @return Supplier
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set ogrn
     *
     * @param string $ogrn
     *
     * @return Supplier
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn
     *
     * @return string
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * Set kpp
     *
     * @param string $kpp
     *
     * @return Supplier
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * Get kpp
     *
     * @return string
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * Set okpo
     *
     * @param string $okpo
     *
     * @return Supplier
     */
    public function setOkpo($okpo)
    {
        $this->okpo = $okpo;

        return $this;
    }

    /**
     * Get okpo
     *
     * @return string
     */
    public function getOkpo()
    {
        return $this->okpo;
    }

    /**
     * Set okved
     *
     * @param string $okved
     *
     * @return Supplier
     */
    public function setOkved($okved)
    {
        $this->okved = $okved;

        return $this;
    }

    /**
     * Get okved
     *
     * @return string
     */
    public function getOkved()
    {
        return $this->okved;
    }

    /**
     * Set okfs
     *
     * @param string $okfs
     *
     * @return Supplier
     */
    public function setOkfs($okfs)
    {
        $this->okfs = $okfs;

        return $this;
    }

    /**
     * Get okfs
     *
     * @return string
     */
    public function getOkfs()
    {
        return $this->okfs;
    }

    /**
     * Set okopf
     *
     * @param string $okopf
     *
     * @return Supplier
     */
    public function setOkopf($okopf)
    {
        $this->okopf = $okopf;

        return $this;
    }

    /**
     * Get okopf
     *
     * @return string
     */
    public function getOkopf()
    {
        return $this->okopf;
    }

    /**
     * Set okato
     *
     * @param string $okato
     *
     * @return Supplier
     */
    public function setOkato($okato)
    {
        $this->okato = $okato;

        return $this;
    }

    /**
     * Get okato
     *
     * @return string
     */
    public function getOkato()
    {
        return $this->okato;
    }

    /**
     * Set director
     *
     * @param string $director
     *
     * @return Supplier
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set basis
     *
     * @param string $basis
     *
     * @return Supplier
     */
    public function setBasis($basis)
    {
        $this->basis = $basis;

        return $this;
    }

    /**
     * Get basis
     *
     * @return string
     */
    public function getBasis()
    {
        return $this->basis;
    }

    /**
     * Get accountant
     *
     * @return string
     */
    public function getAccountant()
    {
        return $this->accountant;
    }

    /**
     * Set accountant
     *
     * @param string $accountant
     *
     * @return Supplier
     */
    public function setAccountant($accountant)
    {
        $this->accountant = $accountant;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Supplier
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * @param mixed $registeredAt
     * @return Supplier
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    /**
     * Set itn
     *
     * @param string $itn
     *
     * @return Supplier
     */
    public function setItn($itn)
    {
        $this->itn = $itn;

        return $this;
    }

    /**
     * Get itn
     *
     * @return string
     */
    public function getItn()
    {
        return $this->itn;
    }

    /**
     * Get checkingAccount
     *
     * @return string
     */
    public function getCheckingAccount()
    {
        return $this->checkingAccount;
    }

    /**
     * Set checkingAccount
     *
     * @param string $checkingAccount
     *
     * @return Supplier
     */
    public function setCheckingAccount($checkingAccount)
    {
        $this->checkingAccount = $checkingAccount;

        return $this;
    }

    /**
     * Get bankShortName
     *
     * @return string
     */
    public function getBankShortName()
    {
        return $this->bankShortName;
    }

    /**
     * Set bankShortName
     *
     * @param string $bankShortName
     *
     * @return Supplier
     */
    public function setBankShortName($bankShortName)
    {
        $this->bankShortName = $bankShortName;

        return $this;
    }

    /**
     * Get bankFullName
     *
     * @return string
     */
    public function getBankFullName()
    {
        return $this->bankFullName;
    }

    /**
     * Set bankFullName
     *
     * @param string $bankFullName
     *
     * @return Supplier
     */
    public function setBankFullName($bankFullName)
    {
        $this->bankFullName = $bankFullName;

        return $this;
    }

    /**
     * Get correspondentAccount
     *
     * @return string
     */
    public function getCorrespondentAccount()
    {
        return $this->correspondentAccount;
    }

    /**
     * Set correspondentAccount
     *
     * @param string $correspondentAccount
     *
     * @return Supplier
     */
    public function setCorrespondentAccount($correspondentAccount)
    {
        $this->correspondentAccount = $correspondentAccount;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set bic
     *
     * @param string $bic
     *
     * @return Supplier
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bankMailingAddress
     *
     * @return string
     */
    public function getBankMailingAddress()
    {
        return $this->bankMailingAddress;
    }

    /**
     * Set bankMailingAddress
     *
     * @param string $bankMailingAddress
     *
     * @return Supplier
     */
    public function setBankMailingAddress($bankMailingAddress)
    {
        $this->bankMailingAddress = $bankMailingAddress;

        return $this;
    }

    /**
     * Get bankLegalAddress
     *
     * @return string
     */
    public function getBankLegalAddress()
    {
        return $this->bankLegalAddress;
    }

    /**
     * Set bankLegalAddress
     *
     * @param string $bankLegalAddress
     *
     * @return Supplier
     */
    public function setBankLegalAddress($bankLegalAddress)
    {
        $this->bankLegalAddress = $bankLegalAddress;

        return $this;
    }

    /**
     * Get bankActualAddress
     *
     * @return string
     */
    public function getBankActualAddress()
    {
        return $this->bankActualAddress;
    }

    /**
     * Set bankActualAddress
     *
     * @param string $bankActualAddress
     *
     * @return Supplier
     */
    public function setBankActualAddress($bankActualAddress)
    {
        $this->bankActualAddress = $bankActualAddress;

        return $this;
    }

    /**
     * Get bankItn
     *
     * @return string
     */
    public function getBankItn()
    {
        return $this->bankItn;
    }

    /**
     * Set bankItn
     *
     * @param string $bankItn
     *
     * @return Supplier
     */
    public function setBankItn($bankItn)
    {
        $this->bankItn = $bankItn;

        return $this;
    }

    /**
     * Get bankKpp
     *
     * @return string
     */
    public function getBankKpp()
    {
        return $this->bankKpp;
    }

    /**
     * Set bankKpp
     *
     * @param string $bankKpp
     *
     * @return Supplier
     */
    public function setBankKpp($bankKpp)
    {
        $this->bankKpp = $bankKpp;

        return $this;
    }

    /**
     * Set client
     *
     * @param Client $client
     *
     * @return Supplier
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return SupplierLegalForm
     */
    public function getLegalForm()
    {
        return $this->legalForm;
    }

    /**
     * @param SupplierLegalForm $legalForm
     * @return Supplier
     */
    public function setLegalForm($legalForm)
    {
        $this->legalForm = $legalForm;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAddedToBlackList()
    {
        return $this->addedToBlackList;
    }

    /**
     * @param bool $addedToBlackList
     * @return Supplier
     */
    public function setAddedToBlackList($addedToBlackList)
    {
        $this->addedToBlackList = $addedToBlackList;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncidents()
    {
        return $this->incidents;
    }

    /**
     * @param mixed $incidents
     * @return Supplier
     */
    public function setIncidents($incidents)
    {
        $this->incidents = $incidents;
        return $this;
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return string
     */
    public function encodeSupplierCategories()
    {
        $categories = [];

        foreach ($this->supplierCategories as $category) {
            $categories[] = $category->getId();
        }

        return json_encode($categories);
    }

    public function __toString()
    {
        return $this->title;
    }
}

