<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentTemplate
 *
 * @ORM\Table(name="document_template")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\DocumentTemplateRepository")
 */
class DocumentTemplate
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    public function __construct()
    {
        $this->title = '';
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="is_basic", type="boolean", options={"default":"1"})
     */
    private $basic;

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
     * Set code
     *
     * @param string $code
     *
     * @return DocumentTemplate
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
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
     * Set title
     *
     * @param string $title
     *
     * @return DocumentTemplate
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
     * @return boolean
     */
    public function isBasic()
    {
        return $this->basic;
    }

    /**
     * @param boolean $basic
     * @return DocumentTemplate
     */
    public function setBasic($basic)
    {
        $this->basic = $basic;
        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}

