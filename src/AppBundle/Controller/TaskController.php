<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectMember;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\ProtocolMembers;
use AppBundle\Entity\TaskComment;
use AppBundle\Entity\TaskDiff;
use AppBundle\Entity\TaskFile;
use AppBundle\Entity\TaskFileDownloadManager;
use AppBundle\Entity\TaskSiblings;
use AppBundle\Entity\Team;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Service\Export\ProtocolBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\WorkLog;
use AppBundle\Report\GanttChartBuilder;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Service\Export\ReportTasksBuilder;
use AppBundle\Service\Export\TaskStatsBuilder;
use AppBundle\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TaskController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Project tasks list.
     *
     * @Route("/tasks", name="user_created_tasks")
     */
    public function userTaskListAction(Request $request)
    {
        $filters = $request->get('filters', ['type' => 'in', 'status' => [0, 1, 2, 3, 4, 5, 6, 7]]);

        $filters['show-team-tasks'] = !empty($filters['team']) || !empty($filters['department']) ? 1 : 0;

        if (!empty($filters['department'])) {
            $departments = [];
            if (is_array($filters['department'])) {
                $filters['department'] = array_shift($filters['department']);
            }
            /** @var Team $department */
            $department = $this->getTeamRepository()->find($filters['department']);
            $departments[] = $department->getId();
            if ($department->isDepartment() && !empty($department->getParentTeam())) {
                /** @var Team $childTeam */
                foreach ($department->getChildTeams() as $childTeam) {
                    $departments[] = $childTeam->getId();
                    /** @var Team $team */
                    foreach ($childTeam->getChildTeams() as $team) {
                        $departments[] = $team->getId();
                    }
                }
            }
            $filters['department'] = $departments;
        }

        $order = $request->get('order');
        $orderBy = $request->get('orderBy');
        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 25);
        $user = $this->getUser();
        $teams = $this->getTeamRepository()->findAll();

        $tasks = $this->getProjectTaskRepository()->getAvailableTasks($user,
            $filters,
            $orderBy,
            $order,
            $currentPage,
            $perPage
        );
        $projects = $this->getProjectRepository()->getAvailableProjects($user);
        $taskResults = $this->getTaskResultRepository()->findAll();

        $maxRows = $tasks->count();
        $maxPages = ceil($maxRows / $perPage);

        $teamMembers = $this->getUserRepository()->getUsersGroupedByTeams();

        return $this->render('tasks/user_tasks.html.twig', [
            'tasks' => $tasks,
            'projects' => $projects,
            'filters' => $filters,
            'teamMembers' => $teamMembers,
            'statuses' => ProjectTask::getStatusList(),
            'priorities' => ProjectTask::getPriorityList(),
            'project' => new Project(),
            'newTask' => new ProjectTask(),
            'taskResults' => $taskResults,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'order' => $order,
            'orderBy' => $orderBy,
            'perPage' => $perPage,
            'teams' => $teams
        ]);
    }

    /**
     * Project tasks list.
     *
     * @Route("/project/{id}/tasks", name="project_tasks")
     */
    public function projectTasksAction(Request $request)
    {
        $projectId = $request->get('id');
        $filters = $request->get('filters', []);
        $filters['project'] = $projectId;
        $filters['show-team-tasks'] = 1;
        $order = $request->get('order');
        $orderBy = $request->get('orderBy');
        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);

        $user = $this->getUser();

        $taskResults = $this->getTaskResultRepository()->findAll();

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        $teams = $this->getTeamRepository()->findAll();

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }
        $users = $this->getUserRepository()->findAll();

        $tasks = $this->getProjectTaskRepository()->getAvailableTasks(
            $user,
            $filters,
            $orderBy,
            $order,
            $currentPage,
            $perPage
        );

        $maxRows = $tasks->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);
        $companyUsers = $this->getUserRepository()->getUsersGroupedByTeams($project);

        $newTask = new ProjectTask();
        $newTask->setProject($project);

        return $this->render('tasks/project_tasks.html.twig', [
            'project' => $project,
            'tasks' => $tasks,
            'teams' => $teams,
            'statuses' => ProjectTask::getStatusList(),
            'newTask' => $newTask,
            'taskResults' => $taskResults,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'users' => $users,
            'companyUsers' => $companyUsers
        ]);
    }

    /**
     * New project task form.
     *
     * @Route("/project/{id}/tasks/add", name="project_add_task")
     */
    public function addAction(Request $request)
    {
        $projectId = $request->get('id');

        $taskDetails = $request->get('task');

        if (!empty($taskDetails['project'])) {
            $projectId = $taskDetails['project'];
        }

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser()) && $taskDetails['type'] != ProjectTask::TYPE_PROTOCOL ) {
            return $this->redirectToRoute('projects_list');
        }

        if (!empty($taskDetails)) {
            $responsibleUsers = $taskDetails['responsible'];

            if (count($responsibleUsers) > 1) {
                $taskSiblings = new TaskSiblings();

                $this->getEm()->persist($taskSiblings);
            }

            foreach ($responsibleUsers as $responsibleUser){
                $task = new ProjectTask();

                if (!empty($taskSiblings)) {
                    $task->setTaskSiblings($taskSiblings);
                }

                $task = $this->buildTask($task, $project, $taskDetails, $responsibleUser);

                if (!empty($taskDetails['protocolMembers'])) {
                    $members = $taskDetails['protocolMembers'];
                    foreach ($members as $member) {
                        $protocolMember = new ProtocolMembers();
                        /** @var User $member */
                        $member = $this->getUserRepository()->find($member);

                        $protocolMember
                            ->setProtocol($task)
                            ->setMember($member)
                        ;

                        $this->getEm()->persist($protocolMember);
                        $this->getEm()->flush();
                    }
                }
            }
        }

        return $this->redirectToRoute('project_task_details', ['id' => $project->getId(), 'taskId' => $task->getId()]);
    }

    /**
     * New scheduled task form.
     *
     * @Route("/tasks/scheduled-tasks/add", name="add_scheduled_task")
     */
    public function addScheduledAction(Request $request)
    {
        $scheduledTaskDetails = $request->get('task');

        if (!empty($scheduledTaskDetails)) {
            $scheduledTask = new ProjectTask();

            /** @var Project $project */
            $project = $this->getProjectRepository()->find($scheduledTaskDetails['project']);
            $responsibleUser = $this->getUserRepository()->find($scheduledTaskDetails['responsible']);

            $scheduledTask = $this->buildTask($scheduledTask, $project, $scheduledTaskDetails, $responsibleUser);

            $em = $this->getEm();
            $em->persist($scheduledTask);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * New project task form.
     *
     * @Route("/project/{id}/task/{taskId}", name="project_edit_task")
     */
    public function editAction(Request $request)
    {
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');
        $taskDetails = $request->get('task');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $task = $this->getProjectTaskRepository()->find($taskId);

        if (!empty($taskDetails)) {

            $responsibleUser = $taskDetails['responsible'];
            $this->buildTask($task, $project, $taskDetails, $responsibleUser);

            if (!empty($taskDetails['protocolMembers'])) {
                $members = $taskDetails['protocolMembers'];
                /** @var ProtocolMembers $protocolMember */
                $protocolMembers = $this->getProtocolMembersRepository()->findBy([
                    'protocol' => $taskId,
                ]);

                if ($protocolMembers) {
                    foreach ($protocolMembers as $protocolMember) {
                        $this->getEm()->remove($protocolMember);
                        $this->getEm()->flush();
                    }
                }

                foreach ($members as $member) {
                    $protocolMember = new ProtocolMembers();
                    /** @var User $member */
                    $member = $this->getUserRepository()->find($member);

                    $protocolMember
                        ->setProtocol($task)
                        ->setMember($member)
                    ;

                    $this->getEm()->persist($protocolMember);
                    $this->getEm()->flush();
                }
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('project_task_details', ['id' => $project->getId(), 'taskId' => $task->getId()]);
    }

    /**
     * Task details
     *
     * @Route("/project/{id}/task/{taskId}/details", name="project_task_details")
     */
    public function detailsAction(Request $request)
    {
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);

        $taskResults = $this->getTaskResultRepository()->findAll();

        if ($task->getProject()->getId() != $projectId) {
            return $this->redirectToRoute('project_task_details', [
                'id' => $task->getProject()->getId(),
                'taskId' => $task->getId()
            ]);
        }

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $taskChanges = $this->getTaskDiffRepository()->getTaskChanges($task);
        $taskComments = $this->getTaskCommentRepository()->findBy(['task' => $task], ['id' => 'ASC']);
        $taskFiles = $this->getTaskFileRepository()->findBy(['task' => $taskId, 'deleted' => null]);

        $newTask = new ProjectTask();
        $newTask->setProject($project);

        if ($task->getType() == ProjectTask::TYPE_EPIC) {
            $newTask->setEpic($task);
        } elseif ($task->getType() == ProjectTask::TYPE_PROTOCOL) {
            $newTask->setProtocol($task);
        }

        return $this->render('tasks/details.html.twig', [
            'project' => $project,
            'task' => $task,
            'taskChanges' => $taskChanges,
            'taskComments' => $taskComments,
            'taskFiles' => $taskFiles,
            'newTask' => $newTask,
            'taskResults' => $taskResults,
            'daysWeek' => ProjectTask::getDaysOfTheWeekList()
        ]);
    }

    /**
     * Task details
     *
     * @Route("/project/{id}/epic/{epicId}/gantt-chart", name="project_epic_gantt_chart")
     */
    public function generateGanttChartAction(Request $request)
    {
        $epicId = $request->get('epicId');
        $epic = $this->getProjectTaskRepository()->find($epicId);

        $ganttChartBuilder = new GanttChartBuilder($this->get('phpexcel'));
        $ganttChartBuilder->build($epic);

        return $ganttChartBuilder->getResponse();
    }

    /**
     * Task details
     *
     * @Route("/project/{id}/task/{taskId}/work/log", name="project_task_work_log")
     */
    public function workLogAction(Request $request)
    {
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');
        $workLogData = $request->get('workLog');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);

        if ($task->getProject()->getId() != $projectId) {
            return $this->redirectToRoute('projects_list');
        }

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        if (isset($workLogData['timeSpent'])) {
            $this->logWork($task, $workLogData['timeSpent'], $workLogData['loggedDay']);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('project_task_details', ['id' => $project->getId(), 'taskId' => $task->getId()]);
    }

    /**
     * Task details
     *
     * @Route("/project/work/log/{id}/remove", name="work_log_remove")
     */
    public function removeWorkLogAction(Request $request)
    {
        $workLogId = $request->get('id');

        /** @var WorkLog $workLog */
        $workLog = $this->getWorkLogRepository()->find($workLogId);

        if ($workLog->canRemove($this->getUser())) {
            $workLog->getTask()->removeWorkLog($workLog->getLoggedHours());
            $em = $this->getDoctrine()->getManager();
            $em->remove($workLog);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Task subscribe
     *
     * @Route("/project/{id}/task/{taskId}/subscribe", name="task_subscribe")
     */
    public function subscribeTaskAction(Request $request)
    {
        $taskId = $request->get('taskId');
        $task = $this->getProjectTaskRepository()->find($taskId);

        $task->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Task unsubscribe
     *
     * @Route("/project/{id}/task/{taskId}/unsubscribe", name="task_unsubscribe")
     */
    public function unsubscribeTaskAction(Request $request)
    {
        $taskId = $request->get('taskId');
        $task = $this->getProjectTaskRepository()->find($taskId);

        if ($task->getResponsibleUser()->getId() != $this->getUser()->getId() &&
            $task->getControllingUser()->getId() != $this->getUser()->getId()) {
            $task->removeSubscriber($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param ProjectTask $task
     * @param $timeSpent
     * @param $loggedDay
     */
    protected function logWork(ProjectTask $task, $timeSpent, $loggedDay)
    {
        $timesSpent = explode(' ', $timeSpent);
        $timeSpent = 0;
        foreach ($timesSpent as $ts) {
            $ts = !empty($ts) ? WorkLog::getCorrectTimeValue($ts) : 0;
            $timeSpent += $ts;
        }

        if ($timeSpent) {
            $workLog = new WorkLog();
            $workLog
                ->setOwner($this->getUser())
                ->setTask($task)
                ->setLoggedHours($timeSpent)
                ->setLoggedDay(new \DateTime($loggedDay))
            ;

            $em = $this->getDoctrine()->getManager();
            $task->logTimeSpent($timeSpent);

            $em->persist($workLog);
        }
    }

    /**
     * @param ProjectTask $task
     * @param Project $project
     * @param $taskDetails
     * @param $responsibleUser
     * @return ProjectTask
     */
    protected function buildTask(ProjectTask $task, Project $project, $taskDetails, $responsibleUser)
    {
        $em = $this->getDoctrine()->getManager();

        $task->setTitle($taskDetails['title']);
        $task->setType($taskDetails['type']);
        $task->setPriority((int) $taskDetails['priority']);
        $task->setProject($project);
        $task->setDescription(StringUtils::parseLinks($taskDetails['description']));

        if (!empty($taskDetails['startAt'])) {
            $task->setStartAt(new \DateTime($taskDetails['startAt']));
        }

        if (!empty($taskDetails['endAt'])) {
            $task->setEndAt(new \DateTime($taskDetails['endAt']));
        }

        if (!$task->getReporter()) {
            $task->setReporter($this->getUser());
        }

       if (!empty($taskDetails['originalEstimate'])) {
           if ($task->getOriginalEstimate() / 2 >= $task->getTimeSpent()) {
               $task->setOriginalEstimate($taskDetails['originalEstimate']);
           }
        }

        if (!empty($responsibleUser)) {
            $responsible = $this->getUserRepository()->find($responsibleUser);
            $task->setResponsibleUser($responsible);

            if (!$project->isUserPartOfTeam($responsible)) {
                $teamMember = new ProjectMember();
                $teamMember
                    ->setProject($project)
                    ->setMember($responsible)
                ;

                $em->persist($teamMember);
            }
        }

        if (!empty($taskDetails['controllingUser'])) {
            $controllingUser = $this->getUserRepository()->find($taskDetails['controllingUser']);
            $task->setControllingUser($controllingUser);
        }

        if (!empty($taskDetails['project']) && $taskDetails['project'] != $project->getId()) {
            $newProject = $this->getProjectRepository()->find($taskDetails['project']);
            $task->setProject($newProject);
        }

        if (!empty($taskDetails['epic'])) {
            $newEpic = $this->getProjectTaskRepository()->find($taskDetails['epic']);
            $task->setEpic($newEpic);
        } else {
            $task->setEpic(null);
        }

        if (!empty($taskDetails['protocol'])) {
            $protocol = $this->getProjectTaskRepository()->find($taskDetails['protocol']);
            $task->setProtocol($protocol);

            if ($protocol->getEndAt() < $task->getEndAt()) {
                $protocol->setEndAt($task->getEndAt());
            }

            $protocol
                ->addSubscriber($task->getResponsibleUser())
                ->addSubscriber($task->getControllingUser());

            $em->persist($protocol);
        }

        if (!empty($taskDetails['subject'])) {
            $task->setSubject($taskDetails['subject']);
        }

        if (!empty($taskDetails['result'])) {
            $result = $this->getTaskResultRepository()->find($taskDetails['result']);
            $task->setResult($result);
        }

        if (!empty($taskDetails['scheduledPeriod'])) {
            $task->setScheduledPeriod($taskDetails['scheduledPeriod']);
        }

        if (!empty($taskDetails['scheduler'])) {
            $task->setScheduler($taskDetails['scheduler']);
            $task->setStatus(ProjectTask::STATUS_SCHEDULED);
            if (!empty($taskDetails['daysWeek'])) {
                $task->setDaysWeek(json_encode($taskDetails['daysWeek']));
            }
        }

        $task
            ->addSubscriber($task->getResponsibleUser())
            ->addSubscriber($task->getControllingUser());

        if ($task->getResponsibleUser()->getTeam() &&
            $task->getResponsibleUser()->getTeam()->isNeedsTeamLeaderNotification()) {
            $task->addSubscriber($task->getResponsibleUser()->getTeam()->getLeader());
        }

        $em->persist($task);

        $taskChanges = [];

        if (!$task->getId()) {
            $newTask = true;
        } else {
            $newTask = false;

            $uof = $em->getUnitOfWork();
            $uof->computeChangeSets();

            $taskChanges = $this->logChanges($task, $uof->getEntityChangeSet($task));
        }

        $em->flush();

        $recipients = [];
        $telegramTemplate = null;
        $params = [];

        if ($newTask) {
            $recipients = $this->getTaskRecipients($task);
//            $telegramTemplate = 'telegram/task/new.html.twig';
            $params = ['task' => $task];

            if ($recipients) {
                $this->get('service.notification')->sendNotifications(
                    $recipients,
                    $task->getTitle(),
                    'new_task',
                    $this->getUser(),
                    $params
                );
            }

        } elseif (count($taskChanges)) {
            $recipients = $this->getTaskRecipients($task);
//            $telegramTemplate = 'telegram/task/updated.html.twig';
            $params = [
                'task' => $task,
                'taskChanges' => $taskChanges
            ];

            if ($recipients) {
                $this->get('service.notification')->sendNotifications(
                    $recipients,
                    $task->getTitle(),
                    'task_update',
                    $this->getUser(),
                    $params
                );
            }
        }

        return $task;
    }

    /**
     * @param ProjectTask $task
     * @return array
     */
    protected function getTaskRecipients(ProjectTask $task)
    {
        $recipients = [];

        foreach ($task->getSubscribers() as $subscriber) {
            if ($subscriber->getId() == $this->getUser()->getId()) {
                continue;
            }

            $recipients[] = $subscriber;
        }

        return $recipients;
    }

    /**
     * Project task change state.
     *
     * @Route("/project/{id}/change-state/{taskId}/{state}", name="project_task_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $state = $request->get('state');
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');
        $taskDetails = $request->get('task');

        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if ($task->getProject()->getId() != $projectId) {
            return $this->redirectToRoute('projects_list');
        }

        if (!$project->checkGrants($this->getUser()) && $task->getResponsibleUser()->getTeam()->getLeader()->getId() != $this->getUser()->getId()) {
            return $this->redirectToRoute('projects_list');
        }

        if (array_key_exists($state, $task->isScheduler() ? $task->getSchedulerStatusList() : $task->getStatusList())) {
            $oldState = $task->getStatus();
            $task->setStatus($state);
            if ($state == ProjectTask::STATUS_CANCELLED or $state == ProjectTask::STATUS_DONE) {
                $task->setClosedAt(new \DateTime());
            }
            if ($state == ProjectTask::STATUS_NEW && $oldState == ProjectTask::STATUS_DONE) {
                $task->setClosedAt(null);
            }
            if ($state == ProjectTask::STATUS_IN_PROGRESS && $oldState == ProjectTask::STATUS_NEED_APPROVE_RESULT) {
                $task->incrementNumberOfReturn();
            }

            if (isset($taskDetails['originalEstimate'])) {
                $task->setOriginalEstimate($taskDetails['originalEstimate']);
            }
            if (isset($taskDetails['timeSpent'])) {
                $this->logWork($task, $taskDetails['timeSpent'], 'today');
            }
            if (!empty($taskDetails['comment']['text'])) {
                $this->addComment($task, $taskDetails['comment']);
            }

            $task
                ->addSubscriber($task->getResponsibleUser())
                ->addSubscriber($task->getControllingUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);

            $uof = $em->getUnitOfWork();
            $uof->computeChangeSets();

            $taskChanges = $this->logChanges($task, $uof->getEntityChangeSet($task));

            $em->flush();

            $recipients = [];
            $telegramTemplate = null;
            $params = [];

            if (count($taskChanges)) {
                $recipients = $this->getTaskRecipients($task);

                $params = [
                    'task' => $task,
                    'taskChanges' => $taskChanges
                ];
            }

            if ($recipients) {
                $this->get('service.notification')->sendNotifications(
                    $recipients,
                    $task->getTitle(),
                    'task_update',
                    $this->getUser(),
                    $params
                );
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('project_tasks', ['id' => $projectId]);
    }

    /**
     * Add comment to Project task.
     *
     * @Route("/project/{id}/comment/{taskId}", name="project_task_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');

        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if ($task->getProject()->getId() != $projectId) {
            return $this->redirectToRoute('projects_list');
        }

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $taskComment = $this->addComment($task, $comment);

        $task->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $recipients = $this->getTaskRecipients($task);
        $params = [
            'task' => $task,
            'taskComment' => $taskComment
        ];

        if ($recipients) {
            $this->get('service.notification')->sendNotifications(
                $recipients,
                $task->getTitle(),
                'new_comment',
                $this->getUser(),
                $params
            );
        }

        return $this->redirectToRoute('project_task_details', ['id' => $project->getId(), 'taskId' => $task->getId()]);
    }

    /**
     * Upload file api.
     *
     * @Route("/project/{id}/upload-file/{taskId}", name="task_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $taskId = $request->get('taskId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);

        $taskFiles = $request->files->get('files');
        foreach($taskFiles as $taskFile) {
            try {
                $this->validateFile($taskFile);
                $this->processFile($task, $taskFile);
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param ProjectTask $task
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function processFile(ProjectTask $task, $file)
    {

        if ($file instanceof UploadedFile) {
            $projectFile = new TaskFile();
            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $projectFile
                ->setFileName($file->getClientOriginalName())
                ->setFormat($format)
                ->setOwner($this->getUser())
                ->setFileSize($file->getSize())
                ->setProject($task->getProject())
                ->setTask($task)
                ->setUploadedAt(new \DateTime())
            ;

            $projectFile
                ->addUser($this->getUser()->getTeam()->getLeader())
                ->addUser($task->getProject()->getLeader())
                ->addUser($task->getResponsibleUser())
                ->addUser($task->getControllingUser())
            ;

            $this->moveFile($file, $projectFile, $task->getProject()->getId(), $task->getId());

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($projectFile);
            $em->flush();
        }
        $recipients = $this->getTaskRecipients($task);
        $params = [
            'task' => $task,
            'projectFile' => $projectFile
        ];

        if ($recipients) {
            $this->get('service.notification')->sendNotifications(
                $recipients,
                $task->getTitle(),
                'new_attachment',
                $this->getUser(),
                $params
            );
        }
    }
    /**
     * Download file file url.
     *
     * @Route("/project/{id}/task/{taskId}/download/{fileId}/{preview}", name="task_download_file", defaults={"preview": 0})
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var TaskFile $taskFile */
        $taskFile = $this->getTaskFileRepository()->find($fileId);

        if (!$taskFile->getProject()->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        if (!$taskFile->hasAccess($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $taskFileDownloadManager = new TaskFileDownloadManager();
        $taskFileDownloadManager
            ->setTaskFile($taskFile)
            ->setUser($this->getUser())
            ->setDownloadDate(new \DateTime(date('d.m.Y H:i')))
        ;
        $this->getEm()->persist($taskFileDownloadManager);
        $this->getEm()->flush();

        $fileName = $preview ? $taskFile->getStoredPreviewFileName() : $taskFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $taskFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $projectDir = $this->getParameter('project_files_root_dir') . '/' .
            $taskFile->getProject()->getId() . '/' . $taskFile->getTask()->getId() . '/';

        $projectDir .= $taskFile->getStoredFileDir() ? $taskFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($projectDir . $fileName), 200, $headers);
    }

    /**
     * Scheduled tasks list.
     *
     * @Route("/tasks/scheduled-tasks", name="scheduled_tasks")
     */
    public function scheduledTasksAction(Request $request)
    {
        $filters = $request->get('filters',  ['scheduler' => 1, 'status' => 10]);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $user = $this->getUser();

        $scheduledTasks = $this->getProjectTaskRepository()->getScheduledTasks($user,
            $filters,
            $currentPage,
            $perPage
        );

        $taskResults = $this->getTaskResultRepository()->findAll();
        $teamMembers = $this->getUserRepository()->getUsersGroupedByTeams();

        $maxRows = $scheduledTasks->count();
        $maxPages = ceil($maxRows / $perPage);


        return $this->render('tasks/scheduled_tasks.html.twig', [
            'scheduledTasks' => $scheduledTasks,
            'newTask' => new ProjectTask(),
            'filters' => $filters,
            'statuses' => ProjectTask::getSchedulerStatusList(),
            'schedulerTypes' => ProjectTask::getSchedulerTypesList(),
            'priorities' => ProjectTask::getPriorityList(),
            'taskResults' => $taskResults,
            'teamMembers' => $teamMembers,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => $perPage
        ]);
    }

    /**
     * Aggregate report list.
     *
     * @Route("/report/tasks", name="aggregate_report")
     */
    public function reportTasksAction(Request $request)
    {
        $filters = $request->get('filters', []);

        $orderBy = $request->get('orderBy', 'id');
        $order = $request->get('order','DESC');

        $customOrderBy = $request->get('customOrderBy', 'overdueTask');
        $customGroupBy = $request->get('customGroupBy', 'person');

        $employeeTeams = $this->getUserRepository()->getUsersGroupedByTeams();
        $teams = $this->getTeamRepository()->findAll();

        $report = $this->buildReportList($filters, $orderBy, $order, $customOrderBy, $customGroupBy);

        return $this->render('tasks/aggregate_report.html.twig', [
            'filters' => $filters,
            'statuses' => ProjectTask::getStatusList(),
            'report' => $report,
            'customOrderBy' => $customOrderBy,
            'customGroupBy' => $customGroupBy,
            'employeeTeams' => $employeeTeams,
            'teams' => $teams,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @param $filters
     * @param $orderBy
     * @param $order
     * @param $customOrderBy
     * @param $customGroupBy
     * @return array
     */
    protected function buildReportList($filters, $orderBy, $order, $customOrderBy, $customGroupBy)
    {
        $users = $this->getUserRepository()->getEmployees($filters, $orderBy, $order, $currentPage = 1 ,$perPage = 500);

        $totalTask = $this->getProjectTaskRepository()->getTotalTask($filters);
        $completedTask = $this->getProjectTaskRepository()->getCompletedTask($filters);
        $performedTask = $this->getProjectTaskRepository()->getPerformedTask($filters);
        $completeOnTimeTask = $this->getProjectTaskRepository()->getCompletedOnTimeTask($filters);
        $overdueTask = $this->getProjectTaskRepository()->getOverdueTask($filters);
        $totalOverdueNowTask = $this->getProjectTaskRepository()->getTotalOverdueNowTask($filters);

        $percentExecutedWorksTask = [];
        $percentOverdueTask = [];
        $executedOnTimeAndPercent = [];
        $report = [];
        /** @var User $user */
        foreach ($users as $user) {
            if (!empty($completeOnTimeTask[$user->getId()]) and !empty($completedTask[$user->getId()])) {
                $percentExecutedWorksTask[$user->getId()] = $completedTask ? ($completeOnTimeTask[$user->getId()] / $totalTask[$user->getId()]) * 100 : null;
            } else {
                $percentExecutedWorksTask[$user->getId()] = null;
            }

            if (!empty($totalTask[$user->getId()]) and !empty($overdueTask[$user->getId()])) {
                $percentOverdueTask[$user->getId()] = ($overdueTask[$user->getId()] / $totalTask[$user->getId()]) * 100;
            } else {
                $percentOverdueTask[$user->getId()] = null;
            }

            if (!empty($percentExecutedWorksTask[$user->getId()]) and !empty($completeOnTimeTask[$user->getId()])) {
                $executedOnTimeAndPercent[$user->getId()] = $completeOnTimeTask[$user->getId()] * $percentExecutedWorksTask[$user->getId()];
            } else {
                $executedOnTimeAndPercent[$user->getId()] = null;
            }

            if ($customGroupBy == 'person' || $customGroupBy == 'team') {
                if ($customGroupBy == 'person') {
                    $key = $user->getId();
                    $team = '';
                    $name = $user->getLastNameWithInitials();
                    $tTask = !empty($totalTask[$user->getId()]) ? $totalTask[$user->getId()] : null;
                    $cTask = !empty($completedTask[$user->getId()]) ? $completedTask[$user->getId()] : null;
                    $pTask = !empty($performedTask[$user->getId()]) ? $performedTask[$user->getId()] : null;
                    $cotTask = !empty($completeOnTimeTask[$user->getId()]) ? $completeOnTimeTask[$user->getId()] : null;
                    $oTask = !empty($overdueTask[$user->getId()]) ? $overdueTask[$user->getId()] : null;
                    $tonTask = !empty($totalOverdueNowTask[$user->getId()]) ? $totalOverdueNowTask[$user->getId()] : null;
                    $pewTask = $percentExecutedWorksTask[$user->getId()];
                    $poTask = $percentOverdueTask[$user->getId()];
                    $eotPercent = $executedOnTimeAndPercent[$user->getId()];

                } else if ($customGroupBy == 'team' && $user->getTeam() && !$user->getTeam()->isDepartment()) {
                    $project = $this->getProjectRepository()->findOneBy(['team' => $user->getTeam()->getId()]);

                    $key = $user->getTeam() ? $user->getTeam()->getId() : 1000000;
                    $team = $project ? $project->getId() : 1000000;
                    $name = $user->getTeam() ? $user->getTeam()->getTitle() . ' (' . $user->getTeam()->getLeader()->getLastNameWithInitials() . ')' : 'Без команды';
                    $tTask = (!empty($report[$key]['totalTask']) ? $report[$key]['totalTask'] : 0) + (!empty($totalTask[$user->getId()]) ? $totalTask[$user->getId()] : null);
                    $cTask = (!empty($report[$key]['completedTask']) ? $report[$key]['completedTask'] : 0) + (!empty($completedTask[$user->getId()]) ? $completedTask[$user->getId()] : null);
                    $pTask = (!empty($report[$key]['performedTask']) ? $report[$key]['performedTask'] : 0) + (!empty($performedTask[$user->getId()]) ? $performedTask[$user->getId()] : null);
                    $cotTask = (!empty($report[$key]['completeOnTimeTask']) ? $report[$key]['completeOnTimeTask'] : 0) + (!empty($completeOnTimeTask[$user->getId()]) ? $completeOnTimeTask[$user->getId()] : null);
                    $oTask = (!empty($report[$key]['overdueTask']) ? $report[$key]['overdueTask'] : 0) + (!empty($overdueTask[$user->getId()]) ? $overdueTask[$user->getId()] : null);
                    $tonTask = (!empty($report[$key]['totalOverdueNowTask']) ? $report[$key]['totalOverdueNowTask'] : 0) + (!empty($totalOverdueNowTask[$user->getId()]) ? $totalOverdueNowTask[$user->getId()] : null);
                    $pewTask = $tTask ? ($cotTask / $tTask) * 100 : 0;
                    $poTask = $tTask ? ($oTask / $tTask) * 100 : 0;
                    $eotPercent = (!empty($report[$key]['executedOnTimeAndPercent']) ? $report[$key]['executedOnTimeAndPercent'] : 0) + ($executedOnTimeAndPercent[$user->getId()]);
                } else {
                    continue;
                }

                $report[$key] = [
                    'id' => $key,
                    'team' => $team,
                    'username' => $user->getUsername(),
                    'name' => $name,
                    'totalTask' => $tTask,
                    'completedTask' => $cTask,
                    'performedTask' => $pTask,
                    'completeOnTimeTask' => $cotTask,
                    'overdueTask' => $oTask,
                    'totalOverdueNowTask' => $tonTask,
                    'percentExecutedWorksTask' => $pewTask,
                    'percentOverdueTask' => $poTask,
                    'executedOnTimeAndPercent' => $eotPercent
                ];
            }

            if ($customGroupBy == 'department') {
                if ($user->getTeam() && !$user->getTeam()->isDepartment()) {
                    /** @var Team $parentTeam */
                    $parentTeam = $user->getTeam()->getParentTeam();
                    do {
                        if ($parentTeam && $parentTeam->isDepartment() && $parentTeam->getParentTeam()) {
                            $project = $this->getProjectRepository()->findOneBy(['team' => $parentTeam->getId()]);

                            $key = $parentTeam ? $parentTeam->getId() : 0;
                            $team = $project ? $project->getId() : 0;
                            $name = $user->getTeam() ? $parentTeam->getTitle() . ' (' . $parentTeam->getLeader()->getLastNameWithInitials() . ')' : 'Без команды';
                            $tTask = (!empty($report[$key]['totalTask']) ? $report[$key]['totalTask'] : 0) + (!empty($totalTask[$user->getId()]) ? $totalTask[$user->getId()] : null);
                            $cTask = (!empty($report[$key]['completedTask']) ? $report[$key]['completedTask'] : 0) + (!empty($completedTask[$user->getId()]) ? $completedTask[$user->getId()] : null);
                            $pTask = (!empty($report[$key]['performedTask']) ? $report[$key]['performedTask'] : 0) + (!empty($performedTask[$user->getId()]) ? $performedTask[$user->getId()] : null);
                            $cotTask = (!empty($report[$key]['completeOnTimeTask']) ? $report[$key]['completeOnTimeTask'] : 0) + (!empty($completeOnTimeTask[$user->getId()]) ? $completeOnTimeTask[$user->getId()] : null);
                            $oTask = (!empty($report[$key]['overdueTask']) ? $report[$key]['overdueTask'] : 0) + (!empty($overdueTask[$user->getId()]) ? $overdueTask[$user->getId()] : null);
                            $tonTask = (!empty($report[$key]['totalOverdueNowTask']) ? $report[$key]['totalOverdueNowTask'] : 0) + (!empty($totalOverdueNowTask[$user->getId()]) ? $totalOverdueNowTask[$user->getId()] : null);
                            $pewTask = $tTask ? ($cotTask / $tTask) * 100 : 0;
                            $poTask = $tTask ? ($oTask / $tTask) * 100 : 0;
                            $eotPercent = (!empty($report[$key]['executedOnTimeAndPercent']) ? $report[$key]['executedOnTimeAndPercent'] : 0) + ($executedOnTimeAndPercent[$user->getId()]);

                            $report[$key] = [
                                'id' => $key,
                                'team' => $team,
                                'username' => $user->getUsername(),
                                'name' => $name,
                                'totalTask' => $tTask,
                                'completedTask' => $cTask,
                                'performedTask' => $pTask,
                                'completeOnTimeTask' => $cotTask,
                                'overdueTask' => $oTask,
                                'totalOverdueNowTask' => $tonTask,
                                'percentExecutedWorksTask' => $pewTask,
                                'percentOverdueTask' => $poTask,
                                'executedOnTimeAndPercent' => $eotPercent
                            ];

                            $parentTeam = $parentTeam ? $parentTeam->getParentTeam() : '';
                        } else {
                            break;
                        }
                    } while ($parentTeam && $parentTeam->getParentTeam());

                } elseif ($user->getTeam() && $user->getTeam()->isDepartment()) {
                    $project = $this->getProjectRepository()->findOneBy(['team' => $user->getTeam()->getId()]);

                    $key = $user->getTeam() ? $user->getTeam()->getId() : 0;
                    $team = $project ? $project->getId() : 0;
                    $name = $user->getTeam() ? $user->getTeam()->getTitle() . ' (' . $user->getTeam()->getLeader()->getLastNameWithInitials() . ')' : 'Без команды';
                    $tTask = (!empty($report[$key]['totalTask']) ? $report[$key]['totalTask'] : 0) + (!empty($totalTask[$user->getId()]) ? $totalTask[$user->getId()] : null);
                    $cTask = (!empty($report[$key]['completedTask']) ? $report[$key]['completedTask'] : 0) + (!empty($completedTask[$user->getId()]) ? $completedTask[$user->getId()] : null);
                    $pTask = (!empty($report[$key]['performedTask']) ? $report[$key]['performedTask'] : 0) + (!empty($performedTask[$user->getId()]) ? $performedTask[$user->getId()] : null);
                    $cotTask = (!empty($report[$key]['completeOnTimeTask']) ? $report[$key]['completeOnTimeTask'] : 0) + (!empty($completeOnTimeTask[$user->getId()]) ? $completeOnTimeTask[$user->getId()] : null);
                    $oTask = (!empty($report[$key]['overdueTask']) ? $report[$key]['overdueTask'] : 0) + (!empty($overdueTask[$user->getId()]) ? $overdueTask[$user->getId()] : null);
                    $tonTask = (!empty($report[$key]['totalOverdueNowTask']) ? $report[$key]['totalOverdueNowTask'] : 0) + (!empty($totalOverdueNowTask[$user->getId()]) ? $totalOverdueNowTask[$user->getId()] : null);
                    $pewTask = $tTask ? ($cotTask / $tTask) * 100 : 0;
                    $poTask = $tTask ? ($oTask / $tTask) * 100 : 0;
                    $eotPercent = (!empty($report[$key]['executedOnTimeAndPercent']) ? $report[$key]['executedOnTimeAndPercent'] : 0) + ($executedOnTimeAndPercent[$user->getId()]);

                    $report[$key] = [
                        'id' => $key,
                        'team' => $team,
                        'username' => $user->getUsername(),
                        'name' => $name,
                        'totalTask' => $tTask,
                        'completedTask' => $cTask,
                        'performedTask' => $pTask,
                        'completeOnTimeTask' => $cotTask,
                        'overdueTask' => $oTask,
                        'totalOverdueNowTask' => $tonTask,
                        'percentExecutedWorksTask' => $pewTask,
                        'percentOverdueTask' => $poTask,
                        'executedOnTimeAndPercent' => $eotPercent
                    ];

                    /** @var Team $parentTeam */
                    $parentTeam = $user->getTeam()->getParentTeam();
                    do {
                        if ($parentTeam && $parentTeam->isDepartment() && $parentTeam->getParentTeam()) {
                            $project = $this->getProjectRepository()->findOneBy(['team' => $parentTeam->getId()]);

                            $key = $parentTeam ? $parentTeam->getId() : 0;
                            $team = $project ? $project->getId() : 0;
                            $name = $user->getTeam() ? $parentTeam->getTitle() . ' (' . $parentTeam->getLeader()->getLastNameWithInitials() . ')' : 'Без команды';
                            $tTask = (!empty($report[$key]['totalTask']) ? $report[$key]['totalTask'] : 0) + (!empty($totalTask[$user->getId()]) ? $totalTask[$user->getId()] : null);
                            $cTask = (!empty($report[$key]['completedTask']) ? $report[$key]['completedTask'] : 0) + (!empty($completedTask[$user->getId()]) ? $completedTask[$user->getId()] : null);
                            $pTask = (!empty($report[$key]['performedTask']) ? $report[$key]['performedTask'] : 0) + (!empty($performedTask[$user->getId()]) ? $performedTask[$user->getId()] : null);
                            $cotTask = (!empty($report[$key]['completeOnTimeTask']) ? $report[$key]['completeOnTimeTask'] : 0) + (!empty($completeOnTimeTask[$user->getId()]) ? $completeOnTimeTask[$user->getId()] : null);
                            $oTask = (!empty($report[$key]['overdueTask']) ? $report[$key]['overdueTask'] : 0) + (!empty($overdueTask[$user->getId()]) ? $overdueTask[$user->getId()] : null);
                            $tonTask = (!empty($report[$key]['totalOverdueNowTask']) ? $report[$key]['totalOverdueNowTask'] : 0) + (!empty($totalOverdueNowTask[$user->getId()]) ? $totalOverdueNowTask[$user->getId()] : null);
                            $pewTask = $tTask ? ($cotTask / $tTask) * 100 : 0;
                            $poTask = $tTask ? ($oTask / $tTask) * 100 : 0;
                            $eotPercent = (!empty($report[$key]['executedOnTimeAndPercent']) ? $report[$key]['executedOnTimeAndPercent'] : 0) + ($executedOnTimeAndPercent[$user->getId()]);

                            $report[$key] = [
                                'id' => $key,
                                'team' => $team,
                                'username' => $user->getUsername(),
                                'name' => $name,
                                'totalTask' => $tTask,
                                'completedTask' => $cTask,
                                'performedTask' => $pTask,
                                'completeOnTimeTask' => $cotTask,
                                'overdueTask' => $oTask,
                                'totalOverdueNowTask' => $tonTask,
                                'percentExecutedWorksTask' => $pewTask,
                                'percentOverdueTask' => $poTask,
                                'executedOnTimeAndPercent' => $eotPercent
                            ];

                            $parentTeam = $parentTeam ? $parentTeam->getParentTeam() : '';
                        } else {
                            break;
                        }
                    } while ($parentTeam && $parentTeam->getParentTeam());
                }
            }
        }

        if ($customOrderBy && $customOrderBy != 'username') {
            usort($report, function ($a, $b) use ($customOrderBy, $order) {
                return $order == 'asc' ?
                    $a[$customOrderBy] - $b[$customOrderBy] :
                    $b[$customOrderBy] - $a[$customOrderBy];
            });
        }

        return $report;
    }

    /**
     * Export report action.
     *
     * @Route("/report/tasks/export-report", name="export_report_tasks")
     */
    public function exportReportTasksAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $orderBy = $request->get('orderBy', 'id');
        $order = $request->get('order','DESC');

        $customOrderBy = $request->get('customOrderBy', 'overdueTask');
        $customGroupBy = $request->get('customGroupBy', 'person');

        $report = $this->buildReportList($filters, $orderBy, $order, $customOrderBy, $customGroupBy);

        $exportBuilder = new ReportTasksBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build(
            $filters,
            $report,
            $customOrderBy,
            $customGroupBy
        );

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            StringUtils::transliterate('aggregate_report') . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * Project tasks list.
     *
     * @Route("/report/tasks/stats", name="report_tasks_stats")
     */
    public function taskStatsAction(Request $request)
    {
        $filters = $request->get('filters', ['type' => 'in', 'status' => [0, 1, 2, 3, 4, 5, 6]]);
        $order = $request->get('order');
        $orderBy = $request->get('orderBy');
        $currentPage = 1;
        $perPage = 500;
        $user = $this->getUser();

        $tasks = $this->getProjectTaskRepository()->getAvailableTasks($user,
            $filters,
            $orderBy,
            $order,
            $currentPage,
            $perPage
        );

        $exportBuilder = new TaskStatsBuilder($this->get('phpexcel'), $this->get('translator'), $this->get('router'));

        $phpExcelObject = $exportBuilder->build($tasks);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'task_stats.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/project/{id}/task/{taskId}/export-protocol", name="export_protocol")
     */
    public function exportProtocolAction(Request $request)
    {
        $projectId = $request->get('id');
        $taskId = $request->get('taskId');

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        /** @var ProjectTask $task */
        $task = $this->getProjectTaskRepository()->find($taskId);

        if ($task->getProject()->getId() != $projectId) {
            return $this->redirectToRoute('project_task_details', [
                'id' => $task->getProject()->getId(),
                'taskId' => $task->getId()
            ]);
        }

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('projects_list');
        }

        $exportBuilder = new ProtocolBuilder();
        $phpWordObject = $exportBuilder->build($task);

        $filename = $task->getTitle() . ' от ' . $task->getStartAt()->format('d.m.y') . 'г.docx';
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

    /**
     * @Route("/project/{id}/task/{taskId}/access-rights/{fileId}/edit", name="task_access_rights_edit")
     */
    public function accessRightsFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $fullAccess = $request->get('fullAccess');
        $usersIds = $request->get('usersIds');

        /** @var TaskFile $file */
        $file = $this->getTaskFileRepository()->find($fileId);

        $file->setFullAccess($fullAccess == 'all' ? true : false);
        $this->getEm()->persist($file);

        if ($fullAccess == 'some') {
            /** @var User $user */
            foreach ($file->getUsers() as $user) {
                $file->removeUser($user);
            }

            if ($usersIds) {
                foreach ($usersIds as $userId) {
                    /** @var User $user */
                    $user = $this->getUserRepository()->find($userId);
                    $file->addUser($user);
                    $this->getEm()->persist($file);
                }
            }
        }

        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @param TaskFile $taskFile
     * @param int $projectId
     * @param int $taskId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, TaskFile $taskFile, $projectId, $taskId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $taskFile->setStoredFileName($storedFileName);
        $taskFile->setStoredFileDir($dirName);

        $basePath = $this->getParameter('project_files_root_dir') . '/' . $projectId . '/' . $taskId . '/' . $dirName;
        // Move the file to the directory where brochures are stored
        $file->move(
            $basePath,
            $storedFileName
        );

        if (in_array($taskFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $taskFile->getFormat();
            $taskFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($basePath . '/' . $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100,100, 25, 25);
            $thumb->writeImage($basePath . '/' . $thumbName);
        }
    }

    /**
     * @param ProjectTask $task
     * @param $comment
     * @return TaskComment
     */
    protected function addComment(ProjectTask $task, $comment)
    {
        $taskComment = new TaskComment();
        $taskComment->setCreatedAt(new \DateTime());
        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $taskComment = $this->getTaskCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $taskComment;
        }

        $changes['comment'][] = $taskComment->getCommentText();
        $taskComment
            ->setOwner($this->getUser())
            ->setTask($task)
            ->setCommentText(StringUtils::parseLinks($comment['text']))
        ;
        $changes['comment'][] = $taskComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getTaskCommentRepository()->find($comment['reply-id']);
            $taskComment->setParentComment($parentComment);
        }

        $this->logChanges($task, $changes);
        $em = $this->getDoctrine()->getManager();
        $em->persist($taskComment);

        return $taskComment;
    }

    /**
     * @param $task
     * @param $changeSet
     * @return array
     */
    protected function logChanges($task, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $taskDiffs = [];
        foreach ($changeSet as $field => $changes) {
            $oldValue = $this->prepareChangesValue($field, $changes[0], $task);
            $newValue = $this->prepareChangesValue($field, $changes[1], $task);
            if ($oldValue != $newValue) {
                $taskDiff = new TaskDiff();

                $taskDiff
                    ->setChangedBy($this->getUser())
                    ->setTask($task)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($taskDiff);
                $taskDiffs[] = $taskDiff;
            }
        }

        return $taskDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @param ProjectTask $task
     * @return int|string
     */
    protected function prepareChangesValue($field, $value, ProjectTask $task)
    {
        if (empty($value) && in_array($field, ['status', 'priority'])) {
            $value = 0;
        }

        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        }

        switch ($field) {
            case 'status':
                $value = $task->isScheduler() ? ProjectTask::getSchedulerStatusList()[$value] : ProjectTask::getStatusList()[$value];
                break;
            case 'priority':
                $value = ProjectTask::getPriorityList()[$value];
                break;
        }

        return $value;
    }

    /**
     * @param $chatId
     * @param $message
     */
    protected function sendTelegramNotification($chatId, $message)
    {
        $params = [
            'text' => $message,
            'chat_id' => $chatId,
            'parse_mode' => 'Markdown'
        ];

        $this->get('old_sound_rabbit_mq.notification_create_producer')->publish(json_encode($params));
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
