<?php

namespace AppBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class WorkLogController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     *
     * @Route("/worklog", name="worklog_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $filters['owner'] = isset($filters['owner']) ? $filters['owner'] : $this->getUser()->getId();
        $currentPage = $request->get('page', 1);

        $workLogs = $this->getWorkLogRepository()->getWorkLogs($filters, $currentPage, self::PER_PAGE);

        $maxRows = $workLogs->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('worklog/list.html.twig', [
            'workLogs' => $workLogs,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters
        ]);
    }
}