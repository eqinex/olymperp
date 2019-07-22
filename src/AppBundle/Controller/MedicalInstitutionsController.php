<?php

namespace AppBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Service\Import\MedicalInstitutionsImport;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class MedicalInstitutionsController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * @Route("/medical-institutions", name="medical_institutions_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        if (!$this->getUser()->canViewMedicalInstitutions()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $medicalInstitutions = $this->getMedicalInstitutionRepository()->getAvailableMedicalInstitutions($filters, $currentPage, self::PER_PAGE);
        $categories = $this->getMedicalInstitutionCategoryRepository()->findAll();


        $maxRows = $medicalInstitutions->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('medical_institutions/list.html.twig', [
            'medicalInstitutions' => $medicalInstitutions,
            'categories' => $categories,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @Route("/medical-institutions/import", name="medical_institutions_import")
     */
    public function importMedicalInstitutionsAction(Request $request)
    {
        $importFile = $request->files->get('import_file');

        if (!$this->getUser()->canEditMedicalInstitutions()) {
            return $this->redirectToRoute('homepage');
        }

        $filePath = $this->moveFile($importFile);

        $importBuilder = new MedicalInstitutionsImport($this->getDoctrine());
        $importBuilder->build($filePath);

        unlink($filePath);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function moveFile(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();

        $filePath = sys_get_temp_dir() . '/' . $fileName;

        $file->move(
            sys_get_temp_dir(),
            $fileName
        );

        return $filePath;
    }
}