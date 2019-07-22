<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierLegalForm
 *
 * @ORM\Table(name="supplier_legal_form")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\SupplierLegalFormRepository")
 */
class SupplierLegalForm
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
     * @ORM\Column(name="key", type="string", length=255, unique=true)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return SupplierLegalForm
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return SupplierLegalForm
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function __toString()
    {
        return $this->key;
    }
}