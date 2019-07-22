<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.03.19
 * Time: 15:15
 */

namespace WarehouseBundle\Service\Import;

use AppBundle\Repository\RepositoryAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use WarehouseBundle\Entity\Nomenclature;
use WarehouseBundle\Entity\NomenclatureGroup;

class NomenclatureImport
{
    use RepositoryAwareTrait;

    /**
     * @var Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return Registry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param $filePath
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function build($filePath)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $xml = simplexml_load_file($filePath);

        $groups = $xml->{'Классификатор'}->{'Группы'}->{'Группа'};

        foreach ($groups as $group) {
            $code = $group->{'Ид'};
            $name = $group->{'Наименование'};
            /** @var NomenclatureGroup $nomenclatureGroup */
            $nomenclatureGroup = $this->getNomenclatureGroupRepository()->findOneBy(['code' => $code]);

            if (!$nomenclatureGroup) {
                $nomenclatureGroup = new NomenclatureGroup();
                $nomenclatureGroup->setCode($code);
            }

            $nomenclatureGroup
                ->setCode($code)
                ->setName($name)
            ;

            $em->persist($nomenclatureGroup);
            $em->flush();

            if (!empty($group->{'Группы'})) {
                $childGroups = $group->{'Группы'}->{'Группа'};
                $parentGroup = $nomenclatureGroup;
                foreach ($childGroups as $childGroup) {
                    $this->createdGroups($childGroup, $parentGroup);
                }
            }
        }

        $products = $xml->{'Каталог'}->{'Товары'}->{'Товар'};

        foreach ($products as $product) {
            $code = $product->{'Ид'};
            $name = $product->{'Наименование'};
            $vendorCode = $product->{'Артикул'};
            $barcode = $product->{'Штрихкод'};
            $groupCode = $product->{'Группы'}->{'Ид'};

            /** @var NomenclatureGroup $group */
            $group = $this->getNomenclatureGroupRepository()->findOneBy(['code' => $groupCode]);

            /** @var Nomenclature $nomenclature */
            $nomenclature = $this->getNomenclatureRepository()->findOneBy(['code' => $code]);

            if (!$nomenclature) {
                $nomenclature = new Nomenclature();
                $nomenclature->setCode($code);
            }

            $nomenclature
                ->setName($name)
                ->setVendorCode($vendorCode)
                ->setBarcode($barcode ? : null)
                ->setGroup($group)
            ;

            $em->persist($nomenclature);
            $em->flush();
        }

    }

    /**
     * @param $childGroup
     * @param NomenclatureGroup $parentGroup
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function createdGroups($childGroup, NomenclatureGroup $parentGroup)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $code = $childGroup->{'Ид'};
        $name = $childGroup->{'Наименование'};
        /** @var NomenclatureGroup $nomenclatureGroup */
        $nomenclatureGroup = $this->getNomenclatureGroupRepository()->findOneBy(['code' => $code]);

        if (!$nomenclatureGroup) {
            $nomenclatureGroup = new NomenclatureGroup();
            $nomenclatureGroup->setCode($code);
        }

        $nomenclatureGroup
            ->setName($name)
            ->setParentGroup($parentGroup)
        ;

        $em->persist($nomenclatureGroup);
        $em->flush();

        if (!empty($childGroup->{'Группы'})) {
            $childGroups = $childGroup->{'Группы'}->{'Группа'};
            $parentGroup = $nomenclatureGroup;
            foreach ($childGroups as $childGroup) {
                $this->createdGroups($childGroup, $parentGroup);
            }
        }
    }
}