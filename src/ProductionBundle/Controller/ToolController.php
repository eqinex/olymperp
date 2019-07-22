<?php

namespace ProductionBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use ProductionBundle\Entity\Tool;
use ProductionBundle\Entity\ToolWorkLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ToolController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * Finds and displays all production tools.
     *
     * @Route("/production/tools", name="production_tools_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $user = $this->getUser();

        if (!$user->canViewToolWorkLog()) {
            return $this->redirectToRoute('homepage');
        }

        $tools = $this->getToolRepository()->getTools($filters, $currentPage, self::PER_PAGE);

        $maxRows = $tools->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('production/tools/list.html.twig', [
            'tools' => $tools->getIterator(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * Finds and displays details.
     *
     * @Route("/production/tools/{id}", name="production_tools_details")
     */
    public function detailsAction (Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewToolWorkLog()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $filters = $request->get('filters');
        $currentPage = $request->get('page', 1);
        $toolId = $request->get('id');
        /** @var Tool $tool */
        $tool = $this->getToolRepository()->find($toolId);

        $projects = $this->getProjectRepository()->findAll();

        $toolWorkLog = $this->getToolWorkLogRepository()->getToolWorkLog($toolId, $filters, $currentPage, self::PER_PAGE);

        $maxRows = $toolWorkLog->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('production/tools/details.html.twig', [
            'tool' => $tool,
            'toolWorkLog' => $toolWorkLog,
            'projects' => $projects,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * add printers form.
     *
     * @Route("/production/tools/{id}/add-toolWorkLog", name="tool_work_log_add")
     */

    public function addItemsAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewToolWorkLog()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $toolId = $request->get('id');
        $toolWorkLogDetails = $request->get('item');
        $project = $this->getProjectRepository()->find($toolWorkLogDetails['project']);
        /** @var Tool $tool */
        $tool = $this->getToolRepository()->find($toolId);

        $toolWorkLogItem = new ToolWorkLog();
        $toolWorkLogItem
            ->setDesignation($toolWorkLogDetails['designation'])
            ->setTitle($toolWorkLogDetails['title'])
            ->setProject($project)
            ->setTool($tool)
            ->setOwner($user)
            ->setQuantity($toolWorkLogDetails['quantity'])
            ->setConsumptionOfBasicMaterials($toolWorkLogDetails['consumptionOfBasicMaterials'])
            ->setSupportMaterialsConsumption($toolWorkLogDetails['supportMaterialsConsumption'])
            ->setPlacement($toolWorkLogDetails['placement'])
            ->setPrintingTime($toolWorkLogDetails['printingTime']);

        $this->getEm()->persist($toolWorkLogItem);

        $this->getEm()->flush();

        return $this->redirectToRoute('production_tools_details', ['id' => $toolId]);
    }

    /**
     * edit printers form.
     *
     * @Route("/production/tools/{id}/edit-toolWorkLog/{workLogId}", name="tool_work_log_edit")
     */

    public function editAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewToolWorkLog()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $toolId = $request->get('id');
        $workLogId = $request->get('workLogId');
        $toolWorkLogDetails = $request->get('item');

        $project = $this->getProjectRepository()->find($toolWorkLogDetails['project']);
        /** @var Tool $tool */
        $tool = $this->getToolRepository()->find($toolId);

        $toolWorkLogItem = $this->getToolWorkLogRepository()->findOneBy([
            'id' => $workLogId
        ]);

        $toolWorkLogItem
            ->setDesignation($toolWorkLogDetails['designation'])
            ->setTitle($toolWorkLogDetails['title'])
            ->setProject($project)
            ->setTool($tool)
            ->setQuantity($toolWorkLogDetails['quantity'])
            ->setConsumptionOfBasicMaterials($toolWorkLogDetails['consumptionOfBasicMaterials'])
            ->setSupportMaterialsConsumption($toolWorkLogDetails['supportMaterialsConsumption'])
            ->setPlacement($toolWorkLogDetails['placement'])
            ->setPrintingTime($toolWorkLogDetails['printingTime']);

        $this->getEm()->persist($toolWorkLogItem);
        $this->getEm()->flush();

        return $this->redirectToRoute('production_tools_details', ['id' => $toolId]);
    }
}
