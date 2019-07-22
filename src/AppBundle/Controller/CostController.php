<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cost;
use AppBundle\Entity\Project;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CostController extends Controller
{
    const PER_PAGE = 50;

    use RepositoryAwareTrait;
    /**
     * @Route("/project/{id}/costs/list", name="project_costs_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $projectId = $request->get('id');
        $filters['project'] = $projectId;
        $currentPage = $request->get('page', 1);
        if (!$this->getUser()->hasAccessToProjectCosts()) {
            return $this->redirectToRoute('projects_list');
        }
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $costs = $this->getProjectCostRepository()->getProjectCosts(
            $filters,
            $currentPage,
            self::PER_PAGE
        );
        
        $requestCosts = $this->getRequestItemRepository()->getRequestCosts($projectId);
        $HRCosts = $this->getWorkLogRepository()->getProjectWorkLog($projectId) * 350;

        $maxRows = $costs->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('costs/project_costs.html.twig', [
            'priorities' => Project::getPriorityChoices(),
            'filters' => $filters,
            'project' => $project,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'costs' => $costs,
            'totalCost' => ($this->getProjectCostRepository()->getProjectCostTotal($projectId) +
                $requestCosts + $HRCosts),
            'requestCosts' => $requestCosts,
            'HRCosts' => $HRCosts
        ]);
    }

    /**
     * New project cost form.
     *
     * @Route("/project/{id}/costs/add", name="project_add_cost")
     */
    public function addAction(Request $request)
    {
        $projectId = $request->get('id');
        $costDetails = $request->get('cost');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $cost = new Cost();

        if (!empty($costDetails)) {
            $cost
                ->setTitle($costDetails['title'])
                ->setAmount($costDetails['amount'])
                ->setOwner($this->getUser())
                ->setPaymentType($costDetails['payment_type'])
                ->setCategory($costDetails['category'])
                ->setProject($project);

            $this->getEm()->persist($cost);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Delete cost action.
     *
     * @Route("/project/{id}/delete/{costId}/", name="project_delete_cost")
     */
    public function deleteCostAction(Request $request)
    {
        $costId = $request->get('costId');

        $cost = $this->getProjectCostRepository()->find($costId);

        /** @var Cost $cost */
        if ($cost->canDelete($this->getUser())) {
            $cost->setDeleted(true);

            $this->getEm()->persist($cost);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
