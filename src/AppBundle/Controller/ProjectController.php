<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CategoryPrice;
use AppBundle\Entity\File;
use AppBundle\Entity\PriceIteration;
use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectDiff;
use AppBundle\Entity\ProjectFile;
use AppBundle\Entity\ProjectMember;
use AppBundle\Entity\ProjectPassport;
use AppBundle\Entity\ProjectPassportCategory;
use AppBundle\Entity\ProjectPrice;
use AppBundle\Entity\ProjectStageProgress;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Report\MonthlyReportBuilder;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Service\Export\ProjectRequestsBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use PurchaseBundle\Entity\PurchaseRequestCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ProjectController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("/projects", name="projects_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters') ? $this->cleanUpFilters($request->get('filters')) : [];
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'priority');

        $leaders = [];

        if ($this->getUser()->hasFullAccess()){
            $leaders = $this->getProjectLeaders();
        }

        $categories = $this->getProjectCategoryRepository()->findAll();
        $allowedProjects = $this->getProjectRepository()->getAvailableProjects($this->getUser(), [], $orderBy, $order);
        $projects = $this->getProjectRepository()->getAvailableProjects($this->getUser(), $filters, $orderBy, $order);
        $states = $this->getProjectStatusRepository()->findAll();

        return $this->render('projects/index.html.twig', [
            'allowedProjects' => $allowedProjects,
            'projects' => $projects,
            'categories' => $categories,
            'priorities' => Project::getPriorityChoices(),
            'filters' => $filters,
            'leaders' => $leaders,
            'states' => $states,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @Route("/projects/export-project-requests", name="export_project_requests")
     */
    public function exportProjectRequestsAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'priority');
        $user = $this->getUser();

        $projects = $this->getProjectRepository()->getAvailableProjects($user, $filters, $orderBy, $order);

        $exportBuilder = new ProjectRequestsBuilder($this->get('phpexcel'), $this->get('translator'), $this->get('router'), $this->getDoctrine());

        $phpExcelObject = $exportBuilder->build($projects);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'project_requests.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/projects/list/export", name="project_list_export")
     */
    public function exportListAction(Request $request)
    {
        $filters = $request->get('filters') ? $this->cleanUpFilters($request->get('filters')) : [];

        $projects = $this->getProjectRepository()->getAvailableProjects($this->getUser(), $filters);

        $reportBuilder = new MonthlyReportBuilder($this->get('phpexcel'));

        $phpExcelObject = $reportBuilder->build($projects);
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'project_report.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/project/edit-category-price", name="edit_category_price")
     * @Method({"POST"})
     */
    public function editCategoryPrice(Request $request)
    {
        $categoryPriceId = $request->get('categoryPriceId');
        $value = $request->get('value');
        $projectId = $request->get('projectId');

        /** @var CategoryPrice $categoryPrice */
        $categoryPrice = $this->getCategoryPriceRepository()->find($categoryPriceId);

        $categoryPrice->setPrice(floatval($value));

        $this->getEm()->persist($categoryPrice);
        $this->getEm()->flush();

        $response = [
            'projectId' => $projectId,
            'categoryPriceId' => $categoryPriceId,
            'value' => $value,
        ];

        return new JsonResponse($response);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}", name="project_details")
     */
    public function detailsAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $projectFiles = $this->getProjectFileRepository()->findBy(['project' => $projectId, 'deleted' => null, 'projectPassport' => null]);
        $companyUsers = $this->getUserRepository()->getUsersGroupedByTeams($project);
        $wares = $project->getWares();

        return $this->render('projects/details.html.twig', [
            'project' => $project,
            'projectFiles' => $projectFiles,
            'companyUsers' => $companyUsers,
            'wares' => $wares
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}/passport", name="project_passport")
     */
    public function passportAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $projectPassports = $this->getProjectPassportRepository()->findAll();

        return $this->render('projects/passport.html.twig', [
            'project' => $project,
            'projectPassports' => $projectPassports
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}/passport/{passportId}/files", name="project_passport_files")
     */
    public function passportFilesAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $passportId = $request->get('passportId');
        /** @var ProjectPassport $projectPassport */
        $projectPassport = $this->getProjectPassportRepository()->find($passportId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $projectPassportFiles = $this->getFileRepository()->findBy([
            'project' => $project->getId(),
            'projectPassport' => $projectPassport->getId(),
            'deleted' => null
        ]);

        return $this->render('projects/passport_files.html.twig', [
            'project' => $project,
            'projectPassport' => $projectPassport,
            'projectPassportFiles' => $projectPassportFiles
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}/workflow", name="project_workflow")
     */
    public function workflowAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $workflow = $project->getCategory()->getProjectFlow();
        $users = $this->getUserRepository()->findAll();

        return $this->render('projects/workflow.html.twig', [
            'workflow' => $workflow,
            'project' => $project,
            'users' => $users
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}/stage", name="project_stage")
     */
    public function stageAction(Request $request)
    {
        $projectId = $request->get('id');
        $stage = $request->get('stage');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        if (!empty($stage['id'])) {
            $projectStageProgress = $this->getProjectStageProgressRepository()->find($stage['id']);
        } else {
            $projectStage = $this->getProjectStageRepository()->find($stage['stageId']);

            $projectStageProgress = new ProjectStageProgress();
            $projectStageProgress->setProject($project);
            $projectStageProgress->setProjectStage($projectStage);
        }

        if (!empty($stage['responsibleId'])) {
            $responsible = $this->getUserRepository()->find($stage['responsibleId']);
        } else {
            $responsible =  $this->getUser();
        }

        $projectStageProgress->setResponsibleUser($responsible);
        $state = !empty($stage['status']) ? $stage['status'] : 'work_not_started';
        $projectStageProgress->setStatus($state);
        $projectStageProgress->setStartAt(new \DateTime($stage['startAt']));
        $projectStageProgress->setEndAt(new \DateTime($stage['endAt']));

        $this->getDoctrine()->getEntityManager()->persist($projectStageProgress);
        $this->getDoctrine()->getEntityManager()->flush();

        return $this->redirectToRoute('project_workflow', ['id' => $project->getId()]);
    }

    /**
     * Display a project history
     *
     * @Route("/project/{id}/history", name="project_history")
     */
    public function historyAction(Request $request)
    {
        $projectId = $request->get('id');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $projectChanges = $this->getProjectDiffRepository()->getProjectChanges($project);

        return $this->render('projects/history.html.twig', [
            "project" => $project,
            "projectChanges" => $projectChanges,
            ]);
    }

    /**
     * Edit project form.
     *
     * @Route("/project/{id}/edit", name="edit_project")
     */
    public function editAction(Request $request)
    {
        $projectId = $request->get('id');
        $projectDetails = $request->get('project');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $newProjectLeader = $this->getUserRepository()->find($projectDetails['leader']);

        $project
            ->setName($projectDetails['name'])
            ->setCode($projectDetails['code'])
            ->setLeader($newProjectLeader)
            ->setPriority($projectDetails['priority'])
            ->setDescription($projectDetails['description'])
            ->setGoal($projectDetails['goal'])
        ;

        $uof = $this->getEm()->getUnitOfWork();
        $uof->computeChangeSets();

        $this->getEm()->persist($project);
        $this->logChanges($project, $uof->getEntityChangeSet($project));

        $this->getEm()->flush();

        return $this->redirectToRoute('projects_list');
    }

    /**
     * @param Project $project
     * @param $changeSet
     * @return array
     */
    protected function logChanges(Project $project, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $projectDiffs = [];
        foreach ($changeSet as $field => $changes) {
            $oldValue = $this->prepareChangesValue($field, $changes[0], $project);
            $newValue = $this->prepareChangesValue($field, $changes[1], $project);
            if ($oldValue != $newValue) {
                $projectDiff = new ProjectDiff();

                $projectDiff
                    ->setChangedBy($this->getUser())
                    ->setTask($project)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($projectDiff);
                $projectDiffs[] = $projectDiff;
            }
        }

        return $projectDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        } elseif (!$value) {
            $value = 'no';
        } elseif ($value === true) {
            $value = 'yes';
        }

        return $value;
    }

    /**
     * Adds team member to a project
     *
     * @Route("/project/{id}/add-team-member", name="project_add_team_member")
     */
    public function addTeamMemberAction(Request $request)
    {
        $projectId = $request->get('id');
        $teamMembers = $request->get('teamMembers');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        foreach ($teamMembers as $teamMember) {
            $user = $this->getUserRepository()->find($teamMember);

            if ($user && $project->canEditProjectMember($this->getUser())) {
                $teamMember = new ProjectMember();
                $teamMember->setMember($user);
                $teamMember->setProject($project);

                $project->getProjectMembers()->add($teamMember);
                $this->getDoctrine()->getEntityManager()->persist($project);
                $this->getDoctrine()->getEntityManager()->persist($teamMember);
            }
        }

        $this->getDoctrine()->getEntityManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove team member of a project
     *
     * @Route("/project/{id}/remove-team-member/{teamMemberId}", name="project_remove_team_member")
     */
    public function removeTeamMemberAction(Request $request)
    {
        $projectId = $request->get('id');
        $teamMemberId = $request->get('teamMemberId');
        
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        $teamMember = $this->getProjectMemberRepository()->findOneBy(['project' => $projectId, 'member' => $teamMemberId]);

        if ($teamMember && $project->canEditProjectMember($this->getUser())) {
            $this->getDoctrine()->getEntityManager()->remove($teamMember);
            $this->getDoctrine()->getEntityManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Upload file api.
     *
     * @Route("/project/{id}/upload-file", name="project_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $projectId = $request->get('id');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $projectFiles = $request->files->get('files');
        foreach($projectFiles as $projectFile) {
            try {
                $this->validateFile($projectFile);

                if ($projectFile instanceof UploadedFile) {
                    $file = new ProjectFile();
                    $format = !(empty($projectFile->guessExtension()))
                        ? $projectFile->guessExtension()
                        : $projectFile->getClientOriginalExtension();

                    $file
                        ->setFileName($projectFile->getClientOriginalName())
                        ->setFormat($format)
                        ->setOwner($this->getUser())
                        ->setFileSize($projectFile->getSize())
                        ->setProject($project)
                        ->setUploadedAt(new \DateTime())
                    ;

                    $this->moveFile($projectFile, $file, $projectId);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($file);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('project_details', ['id' => $project->getId()]);
    }

    /**
     * @Route("/project/{id}/upload-file/{passportId}/project-passport", name="project_passport_upload_file")
     */
    public function uploadPassportPassportFileAction(Request $request)
    {
        $projectId = $request->get('id');
        $projectPassportID = $request->get('passportId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        /** @var ProjectPassport $projectPassport */
        $projectPassport = $this->getProjectPassportRepository()->find($projectPassportID);

        $projectPassportFiles = $request->files->get('files');

        foreach($projectPassportFiles as $projectPassportFile) {
            try {
                $this->validateFile($projectPassportFile);

                if ($projectPassportFile instanceof UploadedFile) {
                    $file = new ProjectFile();
                    $format = !(empty($projectPassportFile->guessExtension()))
                        ? $projectPassportFile->guessExtension()
                        : $projectPassportFile->getClientOriginalExtension();

                    $file
                        ->setFileName($projectPassportFile->getClientOriginalName())
                        ->setFormat($format)
                        ->setOwner($this->getUser())
                        ->setFileSize($projectPassportFile->getSize())
                        ->setProject($project)
                        ->setUploadedAt(new \DateTime())
                        ->setProjectPassport($projectPassport)
                    ;

                    $this->moveFile($projectPassportFile, $file, $projectId);

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($file);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('project_passport', ['id' => $project->getId()]);
    }

    /**
     * Download file file url.
     *
     * @Route("/project/{id}/download/{fileId}/{preview}", name="project_download_file", defaults={"preview": 0})
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var ProjectFile $projectFile */
        $projectFile = $this->getProjectFileRepository()->find($fileId);

        if (!$projectFile->getProject()->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $fileName = $preview ? $projectFile->getStoredPreviewFileName() : $projectFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $projectFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $projectDir = $this->getParameter('project_files_root_dir') . '/' . $projectFile->getProject()->getId() . '/';

        $projectDir .= $projectFile->getStoredFileDir() ? $projectFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($projectDir . $fileName), 200, $headers);
    }

    /**
     * Delete file action.
     *
     * @Route("/project/{id}/remove/{fileId}/", name="project_remove_file")
     */
    public function removeFileAction(Request $request)
    {
        $fileId = $request->get('fileId');

        $file = $this->getFileRepository()->find($fileId);

        /** @var File $file */
        if ($file->canDeleteFile($this->getUser())) {
            $file->setDeleted(true);

            $this->getEm()->persist($file);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @param $projectFile
     * @param $projectId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, ProjectFile $projectFile, $projectId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $projectFile->setStoredFileName($storedFileName);
        $projectFile->setStoredFileDir($dirName);

        // Move the file to the directory where brochures are stored
        $file->move(
            $this->getParameter('project_files_root_dir') . '/' . $projectId . '/' . $dirName,
            $storedFileName
        );

        if (in_array($projectFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $projectFile->getFormat();
            $projectFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($this->getParameter('project_files_root_dir') . '/' . $projectId . '/' . $dirName  . '/' .  $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100,100, 25, 25);
            $thumb->writeImage($this->getParameter('project_files_root_dir') . '/' . $projectId . '/' . $dirName  . '/' . $thumbName);
        }
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/project/{id}/calendar", name="project_calendar")
     */
    public function calendarAction(Request $request)
    {
        $projectId = $request->get('id');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        return $this->render('projects/calendar.html.twig', ['project' => $project]);
    }

    /**
     * @Route ("/project/{id}/price-management", name="project_price_management")
     */
    public function priceManagementAction(Request $request)
    {
        $projectId = $request->get('id');

        if (!$this->getUser()->canViewProjectPrice()) {
            return $this->redirectToRoute('homepage');
        }

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        /** @var ProjectPrice $projectPrice */
        $projectPrice = $this->getProjectPriceRepository()->findOneBy(['project' => $projectId]);

        $purchaseCategories = $this->getPurchaseRequestCategoriesRepository()->findAll();
        if (!empty($projectPrice)) {
            $purchaseCategories = array_diff($purchaseCategories, $projectPrice->getCategories());
        }

        return $this->render('projects/price_management.html.twig', [
            'project' => $project,
            'projectPrice' => $projectPrice,
            'purchaseCategories' => $purchaseCategories
        ]);
    }

    /**
     * @Route ("/project/{id}/add-iteration", name="project_price_add_iteration")
     */
    public function addIterationAction(Request $request)
    {
        $projectId = $request->get('id');
        $categoriesId = $request->get('categories');
        $em = $this->getEm();

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        /** @var ProjectPrice $projectPrice */
        $projectPrice = $this->getProjectPriceRepository()->findOneBy(['project' => $projectId]);

        $categories = [];
        if ($categoriesId) {
            foreach ($categoriesId as $categoryId) {
                $category = $this->getPurchaseRequestCategoriesRepository()->find($categoryId);
                $categories[] = $category;
            }
        }

        if (!$categories) {
            $categories = $projectPrice->getCategories();
        }

        if (empty($projectPrice)) {
            /** @var ProjectPrice $projectPrice */
            $projectPrice = new ProjectPrice();
            $projectPrice->setProject($project);
            $em->persist($projectPrice);
        }

        /** @var PriceIteration $priceIteration */
        $priceIteration = new PriceIteration();
        $priceIteration->setProjectPrice($projectPrice);
        $em->persist($priceIteration);

        foreach ($categories as $category) {
            $categoryPrice = $this->buildCategoryPrice($priceIteration, $category);
            $this->getEm()->persist($categoryPrice);
        }

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route ("/project/{id}/delete-iteration/{iterationId}", name="project_price_delete_iteration")
     */
    public function deleteIterationAction(Request $request)
    {
        $priceIterationId = $request->get('iterationId');
        $em = $this->getEm();

        /** @var PriceIteration $priceIteration */
        $priceIteration = $this->getPriceIterationRepository()->find($priceIterationId);

        if (!empty($priceIteration)) {
            foreach ($priceIteration->getCategoryPrices() as $categoryPrice) {
                $em->remove($categoryPrice);
            }
            $em->remove($priceIteration);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route ("/project/{id}/add-price-category", name="project_price_add_category")
     */
    public function addPriceCategoryAction(Request $request)
    {
        $projectId = $request->get('id');
        $categoryData = $request->get('category');
        $em = $this->getEm();

        /** @var ProjectPrice $projectPrice */
        $projectPrice = $this->getProjectPriceRepository()->findOneBy(['project' => $projectId]);

        /** @var PurchaseRequestCategory $category */
        $category = $this->getPurchaseRequestCategoriesRepository()->find($categoryData['categoryId']);

        foreach ($projectPrice->getIterations() as $iteration) {
            $categoryPrice = $this->buildCategoryPrice($iteration, $category);

            $em->persist($categoryPrice);
        }

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route ("/project/{id}/delete-price-category/{categoryTitle}", name="project_price_delete_category")
     */
    public function deletePriceCategoryAction(Request $request)
    {
        $projectId = $request->get('id');
        $categoryTitle = $request->get('categoryTitle');
        $em = $this->getEm();

        /** @var ProjectPrice $projectPrice */
        $projectPrice = $this->getProjectPriceRepository()->findOneBy(['project' => $projectId]);

        /** @var PurchaseRequestCategory $category */
        $category = $this->getPurchaseRequestCategoriesRepository()->findOneBy(['title' => $categoryTitle]);

        foreach ($projectPrice->getIterations() as $iteration) {
            $categoryPrices = $this->getCategoryPriceRepository()->findBy([
                'priceIteration' => $iteration,
                'category' => $category
            ]);
            if ($categoryPrices) {
                foreach ($categoryPrices as $categoryPrice) {
                    $em->remove($categoryPrice);
                }
            }
        }

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param PriceIteration $priceIteration
     * @param PurchaseRequestCategory $category
     * @return CategoryPrice
     */
    protected function buildCategoryPrice(PriceIteration $priceIteration, PurchaseRequestCategory $category)
    {
        $categoryPrice = new CategoryPrice();

        $categoryPrice
            ->setPriceIteration($priceIteration)
            ->setCategory($category)
            ->setPrice(null);

        return $categoryPrice;
    }

    /**
     * @return array
     */
    protected function getProjectLeaders()
    {
        $users = $this->getUserRepository()->findBy(['admin' => 0]);
        $leaders = [];

        foreach ($users as $user) {
            if ($user->isProjectLeader()) {
                $leaders[] = $user;
            }
        }

        return $users;
    }

    /**
     * @param array $filters
     * @return array
     */
    protected function cleanUpFilters($filters)
    {
        foreach ($filters as $filter => $value) {
            if (empty($value)) {
                unset($filters[$filter]);
            }
        }

        return $filters;
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->getSize() > 102400000) {
            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
        }
    }
}
