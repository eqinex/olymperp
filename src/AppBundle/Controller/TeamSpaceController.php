<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DayOff;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Service\Export\CalendarTeamBuilder;
use AppBundle\Service\Export\JobReportBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class TeamSpaceController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("/teams", name="team_spaces_list")
     */
    public function listAction(Request $request)
    {
        $projects = $this->getProjectRepository()->getAvailableTeamSpaces($this->getUser());

        return $this->render('teams/list.html.twig', [
            'projects' => $projects,
            'endYear' => $endYear = new \DateTime('31.12.2030'),
        ]);
    }
    
    /**
     * @return array
     */
    protected function getProjectLeaders()
    {
        $users = $this->getUserRepository()->findAll();
        $leaders = [];

        foreach ($users as $user) {
            if ($user->isProjectLeader()) {
                $leaders[] = $user;
            }
        }

        return $users;
    }

    /**
     * @Route("/teams/{id}/export-calendar", name="export_calendar")
     */
    public function exportVacationCalendar(Request $request)
    {
        $calendar = $request->get('calendar');
        /** @var FlashBag $flashbag */
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        $translator = $this->get('translator');

        $teamId = $request->get('id');
        /** @var Team $team */
        $team = $this->getTeamRepository()->find($teamId);

        $type = DayOff::TYPE_VACATIONS;
        $year = $calendar['year'];

        $dayOffTeamMembers = $this->getDayOffRepository()->getDayOffTeamMembers($team->getAllTeamMembers(), $calendar['year'], $type);

        if (!$dayOffTeamMembers) {
            $flashbag->add('danger', sprintf($translator->trans('Employees %s do not have vacation for %s year!'), $team->getTitle(), $year));
            return $this->redirect($request->headers->get('referer'));
        }

        $exportBuilder = new CalendarTeamBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($team, $dayOffTeamMembers, $year);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'calendar_team.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/teams/{id}/export-job-report", name="export_job_report")
     */
    public function exportJobReport(Request $request)
    {
        $teamId = $request->get('id');
        $details = $request->get('jobReport');
        $period = $details['endAt'];
        /** @var Team $team */
        $team = $this->getTeamRepository()->find($teamId);
        $user = $this->getUser();

        $tasks = $this->getProjectTaskRepository()->getTeamUserTasks($team, $period);

        $exportBuilder = new JobReportBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($team, $tasks, $user, $period);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'job_report.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
