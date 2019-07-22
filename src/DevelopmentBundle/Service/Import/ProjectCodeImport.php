<?php


namespace DevelopmentBundle\Service\Import;

use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\CompanyCode;
use DevelopmentBundle\Entity\ProjectCode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;

class ProjectCodeImport
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

        $count = 1;
        for ($row = 4; $row <= $highestRow; $row++) {
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);

            if (empty($item[1]) && empty($item[2]) && empty($item[4])) {
                break;
            }

            /** @var CompanyCode $companyCode */
            $companyCode = $this->getCompanyCodeRepository()->findOneBy(['name' => $item[1]]);

            $users = $this->getUserRepository()->findAll();

            /** @var User $responsible */
            $responsible = [];
            /** @var User $user */
            foreach ($users as $user) {
                if ($user->getLastNameWithInitials() == $item[9]) {
                    $responsible = $user;
                }
            }

            $date = PHPExcel_Style_NumberFormat::toFormattedString($item[10],'YYYY-MM-DD' );

            $projectCode = new ProjectCode();

            $projectCode
                ->setCompanyCode($companyCode)
                ->setProjectNumber($item[2])
                ->setProjectStage($item[3] ? : null)
                ->setCreatedYear($item[4] ? : null)
                ->setSubassembly($item[5] ? : null)
                ->setExecution($item[6] ? : null)
                ->setCode($item[7])
                ->setName($item[8] ? : null)
                ->setResponsible($responsible ? : null)
                ->setDateOfRegistration($item[10] ? (new \DateTime($date)) : null)
                ->setInsideCode($item[11] ? : null)
                ->setProjectLocation($item[12] ? : null)
                ->setKitEngineeringDocument($item[13] ? : null)
                ->setProjectStructure($item[14] ? : null)
                ->setRemark($item[15] ? : null)
            ;

            if (!$responsible && !empty($item[9])) {
                $projectCode->setReserveResponsible($item[9]);
            }

            $this->getEm()->persist($projectCode);

            $count += 1;

            if (($count % 100) == 1) {
                $this->getEm()->flush();
                $this->getEm()->getUnitOfWork()->clear();
            }
        }

        $this->getEm()->flush();
    }
}