<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DayOff;
use AppBundle\Entity\ProductionCalendar;
use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\TaskComment;
use AppBundle\Entity\TaskDiff;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\WorkLog;
use AppBundle\Repository\DayOffRepository;
use AppBundle\Repository\ProductionCalendarRepository;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\ProjectTaskRepository;
use AppBundle\Repository\TaskCommentRepository;
use AppBundle\Repository\TaskDiffRepository;
use AppBundle\Repository\TeamRepository;
use AppBundle\Repository\WorkLogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/", name="homepage")
     */
    public function homepageAction(Request $request)
    {
        $teamCode = $request->get('team');
        $selectedWeek = $request->get('week', (new \DateTime('now'))->format('W'));
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

        if ($teamCode) {
            $team = $this->getTeamRepository()->findOneBy(['code' => $teamCode]);
        }

        if (!$team) {
            $team = $this->getTeamRepository()->findOneBy(['leader' => $this->getUser()]);
        }

        if (!$team) {
            $team = $this->getUser()->getTeam();
        }

        $users = $team ? $team->getTeamMembers() : [$this->getUser()];
        
        $latestComments = $this->getTaskCommentRepository()->getLatestComments($users);

        if ($team) {
            foreach ($team->getAllTeamMembers() as $teamMember) {
                $weekDays = $this->getWeekDaysTemplate($selectedWeek);
                $userWeekLog = $this->getWorkLogRepository()->getUserSelectedWeekLog($teamMember, $selectedWeek);

                foreach ($userWeekLog as $dayLog) {
                    $weekDays[$dayLog['loggedDay']] = $dayLog['loggedHours'];
                }

                $teamInfo[] = [
                    'user' => $teamMember,
                    'tasks' => $this->getProjectTaskRepository()->findBy([
                        'responsibleUser' => $teamMember,
                        'status' => [ProjectTask::STATUS_IN_PROGRESS],
                        'type' => ProjectTask::TYPE_TASK
                    ]),
                    'workLog' => $weekDays
                ];
            }

            $teamProject = $this->getProjectRepository()->findOneBy(['team' => $team->getId()]);
        }

        $dayOffs = $this->getDayOffRepository()->getSelectedWeekOff($selectedWeek, $team->getAllTeamMembers());

        $myTasks = $this->getTasksStats(['responsible' => $this->getUser()->getId()]);
        $controlTasks = $this->getTasksStats(['controllingUser' => $this->getUser()->getId()]);
        $assignedTasks = $this->getTasksStats(['team' => $team->getId()]);

        $productionCalendarDays = $this->getProductionCalendarRepository()->findAll();

        return $this->render('dashboard/index.html.twig', [
            'myTasks' => $myTasks,
            'assignedTasks' => $assignedTasks,
            'latestTasks' => $latestTasks,
            'latestComments' => $latestComments,
            'stateNames' => ProjectTask::getStatusList(),
            'team' => $team,
            'teamInfo' => $teamInfo,
            'workLog' => new WorkLog(),
            'selectedWeek' => $selectedWeek,
            'weekDays' => $this->getWeekDays($selectedWeek),
            'teamProject' => $teamProject,
            'controlTasks' => $controlTasks,
            'dayOffs' => $dayOffs,
            'productionCalendarDays' => $productionCalendarDays
        ]);
    }

    /**
     * @param array $filters
     * @return array
     */
    protected function getTasksStats($filters)
    {
        return [
            0 => [
                'status' => ProjectTask::STATUS_NEW,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_NEW
                )
            ],
            1 => [
                'status' => ProjectTask::STATUS_IN_PROGRESS,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_IN_PROGRESS
                )
            ],
            2 => [
                'status' => ProjectTask::STATUS_DONE,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_DONE
                )
            ],
            4 => [
                'status' => ProjectTask::STATUS_NEED_APPROVE,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_NEED_APPROVE
                )
            ],
            5 => [
                'status' => ProjectTask::STATUS_READY_TO_WORK,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_READY_TO_WORK
                )
            ],
            6 => [
                'status' => ProjectTask::STATUS_ON_HOLD,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_ON_HOLD
                )
            ],
            7 => [
                'status' => ProjectTask::STATUS_NEED_APPROVE_RESULT,
                'cnt' => $this->getProjectTaskRepository()->getTasksCount(
                    $filters,
                    ProjectTask::STATUS_NEED_APPROVE_RESULT
                )
            ]
        ];
    }

    /**
     * @param $weekNumber
     * @return array
     */
    protected function getWeekDaysTemplate($weekNumber)
    {
        $selectedWeek = (new \DateTime())->setISODate((new \DateTime())->format('Y'), $weekNumber);

        return [
            $selectedWeek->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
            $selectedWeek->modify('+1 day')->format('Y-m-d') => 0,
        ];
    }

    /**
     * @param $weekNumber
     * @return array
     */
    protected function getWeekDays($weekNumber)
    {
        $selectedWeek = (new \DateTime())->setISODate((new \DateTime())->format('Y'), $weekNumber);

        return [
            $selectedWeek->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
            $selectedWeek->modify('+1 day')->format('j'),
        ];
    }

    /**
     * @return ProjectRepository
     */
    protected function getProjectRepository()
    {
        return $this->getDoctrine()->getRepository(Project::class);
    }

    /**
     * @return ProjectTaskRepository
     */
    protected function getProjectTaskRepository()
    {
        return $this->getDoctrine()->getRepository(ProjectTask::class);
    }

    /**
     * @return TaskDiffRepository
     */
    protected function getTaskDiffRepository()
    {
        return $this->getDoctrine()->getRepository(TaskDiff::class);
    }

    /**
     * @return TaskCommentRepository
     */
    protected function getTaskCommentRepository()
    {
        return $this->getDoctrine()->getRepository(TaskComment::class);
    }

    /**
     * @return WorkLogRepository
     */
    protected function getWorkLogRepository()
    {
        return $this->getDoctrine()->getRepository(WorkLog::class);
    }

    /**
     * @return TeamRepository
     */
    protected function getTeamRepository()
    {
        return $this->getDoctrine()->getRepository(Team::class);
    }

    /**
     * @return DayOffRepository
     */
    private function getDayOffRepository()
    {
        return $this->getDoctrine()->getRepository(DayOff::class);
    }

    /**
     * @return ProductionCalendarRepository
     */
    private function getProductionCalendarRepository()
    {
        return $this->getDoctrine()->getRepository(ProductionCalendar::class);
    }
}
