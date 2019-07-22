<?php

namespace WarehouseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NomenclatureGroup
 *
 * @ORM\Table(name="nomenclature_group")
 * @ORM\Entity(repositoryClass="WarehouseBundle\Repository\NomenclatureGroupRepository")
 */
class NomenclatureGroup
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="WarehouseBundle\Entity\NomenclatureGroup", mappedBy="parentGroup")
     */
    private $childGroups;

    /**
     * @ORM\ManyToOne(targetEntity="WarehouseBundle\Entity\NomenclatureGroup")
     * @ORM\JoinColumn(name="parent_group_id", referencedColumnName="id")
     */
    private $parentGroup;

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
     * Set code
     *
     * @param string $code
     *
     * @return NomenclatureGroup
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
     * Set name
     *
     * @param string $name
     *
     * @return NomenclatureGroup
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return mixed
     */
    public function getChildGroups()
    {
        return $this->childGroups;
    }

    /**
     * @param mixed $childGroups
     * @return NomenclatureGroup
     */
    public function setChildGroups($childGroups)
    {
        $this->childGroups = $childGroups;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    /**
     * @param mixed $parentGroup
     * @return NomenclatureGroup
     */
    public function setParentGroup($parentGroup)
    {
        $this->parentGroup = $parentGroup;
        return $this;
    }
}
