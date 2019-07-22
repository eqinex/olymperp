<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 26.04.19
 * Time: 14:11
 */

namespace PurchaseBundle\Service\Import;

use AppBundle\Repository\RepositoryAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PurchaseBundle\Entity\Supplier;
use Ramsey\Uuid\Uuid;

class SuppliersImport
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

        $objects = $xml->{'Объект'};

        foreach ($objects as $object) {
            $link = $object->{'Ссылка'};
            $options = $link->{'Свойство'};
            if ($options[3]->{'Значение'}) {
                $itn = $options[3]->{'Значение'};
                /** @var Supplier $supplier */
                $supplier = $this->getSupplierRepository()->findOneBy(['itn' => $itn]);

                if (!$supplier) {
                    $supplier = new Supplier();

                    $supplier
                        ->setOneSUniqueCode($options[0]->{'Значение'})
                        ->setItn($options[3]->{'Значение'})
                        ->setTitle($options[2]->{'Значение'})
                        ->setFullTitle($options[1]->{'Значение'} ? : null)
                        ->setKpp($options[4]->{'Значение'} ? : null)
                    ;

                } elseif ($supplier && !$supplier->getOneSUniqueCode()) {
                    do {
                        $code = Uuid::uuid4()->toString();
                        $supplierDuplicate = $this->getSupplierRepository()->findOneBy(['oneSUniqueCode' => $code]);
                        if (!$supplierDuplicate) {
                            $supplier->setOneSUniqueCode($code);
                            $check = true;
                        } else {
                            $check = false;
                        }

                    } while ($check == false);
                }

                $em->persist($supplier);
                $em->flush();
            }
            continue;
        }
    }
}