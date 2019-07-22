<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\Specification;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Service\Export\IttBuilder;
use ProductionBundle\Entity\Ware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecificationController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Project itt list.
     *
     * @Route("/project/{id}/itt", name="project_specification")
     */
    public function projectSpecificationAction(Request $request)
    {
        $projectId = $request->get('id');
        $filters = $request->get('filters', []);
        $filters['project'] = $projectId;
        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 25);

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $specifications = $this->getSpecificationRepository()->getAvailableSpecification($filters, $currentPage, $perPage);

        $maxRows = $specifications->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        $newSpecification = new Specification();
        $newSpecification->setProject($project);

        return $this->render('itt/project_itt.html.twig', [
            'project' => $project,
            'specifications' => $specifications,
            'newSpecification' => $newSpecification,
            'statuses' => ProjectTask::getStatusList(),
            'types' => Specification::getTypesList(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
        ]);
    }

    /**
     * @Route("/project/{id}/itt/add", name="project_add_specification")
     */
    public function addAction(Request $request)
    {
        $projectId = $request->get('id');
        $ittDetails = $request->get('itt');
        $wareId = $ittDetails['ware'];

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        /** @var Ware $ware */
        $ware = $this->getWareRepository()->find($wareId);

        if (!$project->checkGrants($this->getUser()) or $project->getLeader() != $this->getUser() ) {
            return $this->redirectToRoute('projects_list');
        }

        if (!empty($ittDetails)) {

            $itt = new Specification();

            $itt = $this->buildItt($itt, $ware, $ittDetails);

            $itt->setProject($project);

            $em = $this->getEm();
            $em->persist($itt);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/project/{id}/itt/{ittId}/edit", name="project_edit_specification")
     */
    public function editAction(Request $request)
    {
        $ittId = $request->get('ittId');
        $projectId = $request->get('id');
        $ittDetails = $request->get('itt');
        $wareId = $ittDetails['ware'];

        /** @var Specification $itt */
        $itt = $this->getSpecificationRepository()->find($ittId);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        /** @var Ware $ware */
        $ware = $this->getWareRepository()->find($wareId);

        if (!$project->checkGrants($this->getUser()) or $project->getLeader() != $this->getUser()) {
            return $this->redirectToRoute('projects_list');
        }

        if (!empty($ittDetails)) {

            $itt = $this->buildItt($itt, $ware, $ittDetails);

            $em = $this->getEm();
            $em->persist($itt);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Specification $itt
     * @param Ware $ware
     * @param $ittDetails
     * @return Specification
     */
    protected function buildItt(Specification $itt, Ware $ware, $ittDetails)
    {
        $itt
            ->setName($ittDetails['name'])
            ->setUnit($ittDetails['unit'])
            ->setWare($ware)
            ->setType($ittDetails['type'])
            ->setValueTask($ittDetails['valueTask'])
            ->setValueInnerTask($ittDetails['valueInnerTask'])
            ->setDifference($ittDetails['difference'])
            ->setNotice($ittDetails['notice'])
        ;

        return $itt;
    }

    /**
     * @Route("/project/{id}/itt/{ittId}/remove", name="project_remove_specification")
     */
    public function removeAction(Request $request)
    {
        $ittId = $request->get('ittId');
        $projectId = $request->get('id');

        /** @var Specification $itt */
        $itt = $this->getSpecificationRepository()->find($ittId);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser()) or $project->getLeader() != $this->getUser()) {
            return $this->redirectToRoute('projects_list');
        }

        $itt->setDeleted(1);

        $em = $this->getEm();
        $em->persist($itt);
        $em->flush();


        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/project/{id}/export-itt", name="project_export_specification")
     */
    public function exportIttAction(Request $request)
    {
        $projectId = $request->get('id');
        $user = $this->getUser();

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $ittTechnical = $this->getSpecificationRepository()->findBy([
            'project' => $project,
            'deleted' => null,
            'type' => Specification::TYPE_TECHNICAL
        ]);

        $ittFunctional = $this->getSpecificationRepository()->findBy([
            'project' => $project,
            'deleted' => null,
            'type' => Specification::TYPE_FUNCTIONAL
        ]);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $exportBuilder = new IttBuilder($this->get('translator'));
        $phpWordObject = $exportBuilder->build($user, $project, $ittTechnical, $ittFunctional);

        $filename = 'Чек-лист ВТЗ Проект - ' . $project->getName() . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'protocol');

        $writer->save($tmp);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ];

        $response = new Response(file_get_contents($tmp), 200, $headers);

        unlink($tmp);

        return $response;
    }
}