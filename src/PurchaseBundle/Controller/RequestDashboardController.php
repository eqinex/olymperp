<?php

namespace PurchaseBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\TaskComment;
use AppBundle\Entity\TaskDiff;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\WorkLog;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\ProjectTaskRepository;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Repository\TaskCommentRepository;
use AppBundle\Repository\TaskDiffRepository;
use AppBundle\Repository\TeamRepository;
use AppBundle\Repository\WorkLogRepository;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestComment;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\PurchaseConstants;
use PurchaseBundle\Repository\PurchaseRequestCommentRepository;
use PurchaseBundle\Repository\RequestItemRepository;
use PurchaseBundle\Repository\PurchaseRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RequestDashboardController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/requests/dashboard", name="requests_dashboard")
     */
    public function homepageAction(Request $request)
    {
        if (!$this->getUser()->isGranted("ROLE_CAN_VIEW_REQUESTS_DASHBOARD")) {
            return $this->redirectToRoute('homepage');
        }

        $latestTasksDiffs = $this->getTaskDiffRepository()->getLatestChanges($this->getUser());
        $latestTasks = [];
        $teamProject = null;

        foreach ($latestTasksDiffs as $diff) {
            if (!isset($latestTasks[$diff->getTask()->getId()])) {
                $latestTasks[$diff->getTask()->getId()] = $diff->getTask();
            }
        }

        $teamInfo = [];
        /** @var Team $team */
        $team = null;
        $team = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);

        if ($team) {
            foreach ($team->getAllTeamMembers() as $teamMember) {
                $teamInfo[] = [
                    'user' => $teamMember,
                    'requests' => $this->getPurchaseRequestRepository()->findBy([
                        'purchasingManager' => $teamMember,
                        'status' => [PurchaseConstants::STATUS_MANAGER_STARTED_WORK]
                    ])
                ];
            }

            $teamProject = $this->getProjectRepository()->findOneBy(['team' => $team->getId()]);
        }

        $latestComments = $this->getPurchaseRequestCommentRepository()->getLatestComments();
        $paymentStat[0] = $this->getRequestItemRepository()
            ->getRequestPaymentStats(PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT);
        $paymentStat[1] = $this->getRequestItemRepository()
            ->getRequestPaymentStats(PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING);
        $paymentStat[2] = $this->getRequestItemRepository()
            ->getRequestPaymentStats(PurchaseConstants::PAYMENT_STATUS_PAID);

        $myTasks = $this->getRequestStats([]);

        return $this->render('purchase/dashboard.html.twig', [
            'myTasks' => $myTasks,
            'assignedTasks' => [],
            'latestTasks' => $latestTasks,
            'latestComments' => $latestComments,
            'team' => $team,
            'teamInfo' => $teamInfo,
            'teamProject' => $teamProject,
            'paymentStat' => $paymentStat
        ]);
    }

    /**
     * @param array $filters
     * @return array
     */
    protected function getRequestStats($filters)
    {
        return [
            0 => [
                'status' => PurchaseConstants::STATUS_NEW,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_NEW
                )
            ],
            1 => [
                'status' => PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL
                )
            ],
            2 => [
                'status' => PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL
                )
            ],
            3 => [
                'status' => PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER
                )
            ],
            4 => [
                'status' => PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_MANAGER_ASSIGNED
                )
            ],
            5 => [
                'status' => PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_MANAGER_STARTED_WORK
                )
            ],
            6 => [
                'status' => PurchaseConstants::STATUS_MANAGER_FINISHED_WORK,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
                )
            ],
            7 => [
                'status' => PurchaseConstants::STATUS_DONE,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_DONE
                )
            ],
            8 => [
                'status' => PurchaseConstants::STATUS_NEEDS_FIXING,
                'cnt' => $this->getPurchaseRequestRepository()->getRequestsCount(
                    $filters,
                    PurchaseConstants::STATUS_NEEDS_FIXING
                )
            ],
        ];
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/managers/stats", name="manager_stats_dashboard")
     */
    public function managersStatsAction(Request $request)
    {
        $statsDay = new \DateTime();
        
        $statsRepo = $this->getManagerStatsRepository();
        
        $inQueueRequests = $statsRepo->getRequestsInProgress($statsDay);
        
        return $this->render('purchase/monitoring_dashboard.html.twig', [
            'inQueueRequests' => $inQueueRequests
        ]);
    }

}
