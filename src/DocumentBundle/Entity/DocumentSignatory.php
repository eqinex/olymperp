<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentSignatory
 *
 * @ORM\Table(name="document_signatory")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\DocumentSignatoryRepository")
 */
class DocumentSignatory
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
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="signatory_id", referencedColumnName="id")
     */
    private $signatory;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text")
     */
    private $remarks = '';

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
     * Set document
     *
     * @param \stdClass $document
     *
     * @return DocumentSignatory
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \stdClass
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set signatory
     *
     * @param \stdClass $signatory
     *
     * @return DocumentSignatory
     */
    public function setSignatory($signatory)
    {
        $this->signatory = $signatory;

        return $this;
    }

    /**
     * Get signatory
     *
     * @return \stdClass
     */
    public function getSignatory()
    {
        return $this->signatory;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     * @return DocumentSignatory
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     * @return DocumentSignatory
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    /**
     * @param string $remarks
     * @return DocumentSignatory
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }
}