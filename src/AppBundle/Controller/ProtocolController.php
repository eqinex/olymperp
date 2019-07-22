<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProjectTask;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProtocolController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Protocols list.
     *
     * @Route("/protocols", name="protocols_list")
     */
    public function protocolsAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 25);
        $user = $this->getUser();

        $taskResults = $this->getTaskResultRepository()->findAll();

        if (!$user->canViewAllProtocols()) {
            $filters['user'] = $user;
        }
        $tasks = $this->getProjectTaskRepository()->getProtocolTasks($user, $filters, $currentPage, $perPage);

        $maxRows = $tasks->count();
        $maxPages = ceil($maxRows / $perPage);

        return $this->render('tasks/protocols.html.twig', [
            'tasks' => $tasks,
            'filters' => $filters,
            'statuses' => ProjectTask::getStatusList(),
            'priorities' => ProjectTask::getPriorityList(),
            'newTask' => new ProjectTask(),
            'taskResults' => $taskResults,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => $perPage,
        ]);
    }
}
