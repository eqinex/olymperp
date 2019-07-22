<?php


namespace AppBundle\Service\Import;

use AppBundle\Entity\MedicalInstitution;
use AppBundle\Entity\MedicalInstitutionCategory;
use AppBundle\Repository\RepositoryAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PHPExcel_IOFactory;

class MedicalInstitutionsImport
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

        set_time_limit(0);

        for ($row = 2; $row <= $highestRow; $row++) {
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);

            if (empty($item[1]) && empty($item[2]) && empty($item[3])) {
                break;
            }

            $category = $this->getMedicalInstitutionCategoryRepository()->findOneBy(['name' => $item[7]]);

            if (!$category) {
                $category = new MedicalInstitutionCategory();

                $category->setName($item[7]);

                $this->getEm()->persist($category);
                $this->getEm()->flush();
            }
        }

        $count = 1;
        for ($row = 2; $row <= $highestRow; $row++) {

            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $item = current($rows);

            if (empty($item[1]) && empty($item[2]) && empty($item[3])) {
                break;
            }

            /** @var MedicalInstitutionCategory $category */
            $category = $this->getMedicalInstitutionCategoryRepository()->findOneBy(['name' => $item[7]]);

            $medicalInstitution = new MedicalInstitution();

            $medicalInstitution
                ->setName($item[0])
                ->setType($item[1] ? : null)
                ->setRegion($item[2] ? : null)
                ->setCity($item[3] ? : null)
                ->setDistrict($item[4] ? : null)
                ->setIndexMedicalInstitution($item[5] ? : null)
                ->setAddress($item[6] ? : null)
                ->setCategory($category)
                ->setPhone($item[8] ? : null)
                ->setEmail($item[9] ? : null)
                ->setSite($item[10] ? : null)
            ;

            $this->getEm()->persist($medicalInstitution);

            $count += 1;

            if (($count % 100) == 1) {
                $this->getEm()->flush();
                $this->getEm()->getUnitOfWork()->clear();
            }
        }

        $this->getEm()->flush();
    }
}