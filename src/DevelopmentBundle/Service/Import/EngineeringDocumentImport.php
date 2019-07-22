<?php

namespace DevelopmentBundle\Service\Import;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\EngineeringDocument;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PHPExcel_IOFactory;

class EngineeringDocumentImport
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

    public function build($filePath)
    {
        $inputFileType = PHPExcel_IOFactory::identify($filePath);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 5; $row <= $highestRow; $row++) {
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);

            if (empty($item[1]) && empty($item[2]) && empty($item[3])) {
                break;
            }

            $engineeringDocument = new EngineeringDocument();

            if (!empty($item[15])) {
                $projectName = trim($item[15]);
                /** @var Project $project */
                $project = $this->getProjectRepository()->findOneBy([
                    'name' => $projectName
                ]);

                if ($project) {
                    $engineeringDocument->setProject($project);

                    $this->getEm()->persist($engineeringDocument);
                } else {
                    $engineeringDocument->setNotice($projectName);

                    $this->getEm()->persist($engineeringDocument);
                }
            }

            if (!empty($item[13])) {
                $ownerName = $item[13];
                $lastName = explode(' ',trim($ownerName));
                $lastName = $lastName[0];

                /** @var User $owner */
                $owner = $this->getUserRepository()->findOneBy([
                    'lastname' => $lastName
                ]);

                if ($owner) {
                    $engineeringDocument->setOwner($owner);

                    $this->getEm()->persist($engineeringDocument);
                }
            }

            $unixCreatedAt = ($item[2] - 25569) * 86400;

            $engineeringDocument
                ->setInventoryNumber($item[1])
                ->setCreatedAt(new \DateTime(date('d.m.Y',$unixCreatedAt)))
                ->setDesignation($item[3])
                ->setNumberOfPages($item[4])
                ->setFormat($item[5])
                ->setTitle($item[6])
                ->setCode($item[7])
                ->setClass($item[8])
                ->setSubgroup($item[9])
                ->setIndexNumber($item[10])
                ->setDocumentExecution($item[11])
                ->setDecryptionCode($item[12])
            ;

            $this->getEm()->persist($engineeringDocument);
            $this->getEm()->flush();
        }
    }
}