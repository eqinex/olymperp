<?php


namespace DevelopmentBundle\Controller;

use AppBundle\Entity\User;
use DevelopmentBundle\Entity\CompanyCode;
use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\ProjectCode;
use DevelopmentBundle\Service\Import\ProjectCodeImport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DevelopmentBundle\Repository\ProjectCodeRepository;

class ProjectCodeController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * Project code
     *
     * @Route("/development/project-code", name="project_code_list")
     */
    public function listAction(Request $request)
    {

        if (!$this->getUser()->canViewProjectCode()) {
            return $this->redirectToRoute('homepage');
        }

        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        $users = $this->getUserRepository()->findAll();

        $projectCodes = $this->getProjectCodeRepository()->getProjectCodes($filters, $orderBy, $order, $currentPage, self::PER_PAGE);

        $maxRows = $projectCodes->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        $companyCodes = $this->getCompanyCodeRepository()->findAll();
        return $this->render('development/project_code/list.html.twig', [
            'filters' => $filters,
            'users' => $users,
            'projectCodes' => $projectCodes,
            'companyCodes' => $companyCodes,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order,
            'statuses' => ProjectCode::getStageList()
        ]);
    }

    /**
     * Add project code.
     *
     * @Route("/development/project-code/add", name="project_code_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $projectCodeDetails = $request->get('project_code');

        try {
            if (!empty($projectCodeDetails)) {
                $projectCode = new ProjectCode();

                $projectCode = $this->buildProjectCode($projectCode, $projectCodeDetails);

                $em = $this->getEm();
                $em->persist($projectCode);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('project_code_list');
    }

    /**
     * @param ProjectCode $projectCode
     * @param $projectCodeDetails
     * @return ProjectCode
     * @throws \Exception
     */
    public function buildProjectCode(ProjectCode $projectCode, $projectCodeDetails)
    {
        $companyCode = $this->getCompanyCodeRepository()->find($projectCodeDetails['companyCode']);
        $responsible = $this->getUserRepository()->find($projectCodeDetails['responsible']);

        $projectCode
            ->setName($projectCodeDetails['name'])
            ->setRemark($projectCodeDetails['remark'])
            ->setCompanyCode($companyCode)
            ->setProjectStage($projectCodeDetails['projectStage'])
            ->setSubassembly($projectCodeDetails['subassembly'])
            ->setExecution($projectCodeDetails['execution'])
            ->setInsideCode($projectCodeDetails['insideCode'])
            ->setProjectLocation($projectCodeDetails['projectLocation'])
            ->setKitEngineeringDocument($projectCodeDetails['setEngineeringDocument'])
            ->setProjectStructure($projectCodeDetails['projectStructure'])
            ->setCode($projectCodeDetails['code'])
            ->setProjectNumber($projectCodeDetails['projectNumber'])
            ->setCreatedYear($projectCodeDetails['createdYear'])
            ->setResponsible($responsible)
            ->setDateOfRegistration(new \DateTime($projectCodeDetails['dateOfRegistration']))
        ;

        return $projectCode;
    }

    /**
     * Edit project code.
     *
     * @Route("/development/project-code/{id}/edit", name="project_code_edit")
     */
    public function editProjectCode(Request $request)
    {
        if ($this->getUser()->canEditProjectCode()) {
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->clear();

            $projectCodeId = $request->get('id');
            $projectCodeDetails = $request->get('project_code');

            /** @var ProjectCode $projectCode */
            $projectCode = $this->getProjectCodeRepository()->find($projectCodeId);

            try {
                if (!empty($projectCode)) {
                    $projectCode = $this->buildProjectCode($projectCode, $projectCodeDetails);

                    $em = $this->getEm();
                    $em->persist($projectCode);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove project code
     *
     *  @Route("/development/project-code/{id}/remove", name="project_code_remove")
     */
    public function deleteProjectCodeAction(Request $request)
    {
        if ($this->getUser()->canDeleteProjectCode()) {
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->clear();

            $projectCodeId = $request->get('id');

            /** @var ProjectCode $projectCode */
            $projectCode = $this->getProjectCodeRepository()->find($projectCodeId);

            try {
                if (!empty($projectCode)) {
                    $projectCode->setDeleted(true);
                    $em = $this->getEm();
                    $em->persist($projectCode);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Finds and displays details.
     *
     * @Route("/project-code/details/{id}", name="project_code_details")
     */
    public function detailsAction (Request $request)
    {
        $projectCodeId = $request->get('id');

        /** @var projectCode $projectCode */
        $projectCode = $this->getProjectCodeRepository()->find($projectCodeId);

        $users = $this->getUserRepository()->findAll();
        $companyCodes = $this->getCompanyCodeRepository()->findAll();
        return $this->render('development/project_code/details.html.twig', [
            'projectCode' => $projectCode,
            'statuses' => ProjectCode::getStageList(),
            'users' => $users,
            'companyCodes' => $companyCodes,
            ]);
    }

    /**
     * Edit project code details.
     *
     * @Route("/development/project-code/{id}/delails/edit", name="project_code_details_edit")
     */
    public function editDetailsProjectCode(Request $request)
    {
        if ($this->getUser()->canEditProjectCode()) {
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->clear();

            $projectCodeId = $request->get('id');
            $projectCodeDetails = $request->get('project_code');

            /** @var ProjectCode $projectCode */
            $projectCode = $this->getProjectCodeRepository()->find($projectCodeId);

            try {
                if (!empty($projectCode)) {
                    $projectCode = $this->buildProjectCode($projectCode, $projectCodeDetails);

                    $em = $this->getEm();
                    $em->persist($projectCode);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/development/project-code/import", name="project_code_import")
     */
    public function importProjectCodeAction(Request $request)
    {
        $importFile = $request->files->get('import_file');

        if (!$this->getUser()->canEditProjectCode()) {
            return $this->redirectToRoute('homepage');
        }

        $filePath = $this->moveFile($importFile);

        $importBuilder = new ProjectCodeImport($this->getDoctrine());
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