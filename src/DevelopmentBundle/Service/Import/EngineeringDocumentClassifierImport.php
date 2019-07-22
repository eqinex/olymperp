<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 28.03.19
 * Time: 16:55
 */

namespace DevelopmentBundle\Service\Import;

use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\EngineeringDocumentClassifier;
use Doctrine\Bundle\DoctrineBundle\Registry;

class EngineeringDocumentClassifierImport
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

        $csv = trim(file_get_contents($filePath));

        $rows = explode(PHP_EOL, $csv);

        foreach ($rows as $row => $value) {
            $data = explode(';', $value);

            $classifierClass = $data[1];
            $classifierSubgroup = $data[2] != "" ? $data[2] : null;
            $classifierDescription = $data[3];

            $engineeringDocumentClassifier = $this->getEngineeringDocumentClassifierRepository()->findOneBy([
                'class' => $data[1],
                'subgroup' => $data[2]
            ]);

            if (!$engineeringDocumentClassifier) {
                $engineeringDocumentClassifier = new EngineeringDocumentClassifier();

                $engineeringDocumentClassifier
                    ->setClass($classifierClass)
                    ->setSubgroup($classifierSubgroup)
                    ->setDescription($classifierDescription);
            } else {
                $engineeringDocumentClassifier
                    ->setClass($classifierClass)
                    ->setSubgroup($classifierSubgroup)
                    ->setDescription($classifierDescription);
            }
            $em->persist($engineeringDocumentClassifier);
        }
        $em->flush();
    }
}