<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectRole;
use AppBundle\Entity\TaskDiff;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\UserAchievement;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Repository\TaskDiffRepository;
use AppBundle\Repository\TeamRepository;
use AppBundle\Repository\UserAchievementRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ProjectRoleRepository;
use AppBundle\Repository\DayOffRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class EmployeeController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Finds and displays all employees.
     *
     * @Route("/employees", name="employees_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        $employees = $this->getUserRepository()->getEmployees(
            $filters,
            $orderBy,
            $order,
            $currentPage,
            self::PER_PAGE);

        $maxRows = $employees->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        $teams = $this->getTeamRepository()->findAll();
        $employeeStatuses = User::getEmployeeStatuses();
        $employeeTeams = $this->getUserRepository()->getUsersGroupedByTeams();
        $employeeRoles = $this->getProjectRoleRepository()->findAll();

        return $this->render('employee/list.html.twig', [
            'employees' => $employees->getIterator(),
            'employeeTeams' => $employeeTeams,
            'teams' => $teams,
            'employeeStatuses' => $employeeStatuses,
            'employeeRoles' => $employeeRoles,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order,
            'genders' => User::getGenderList()
        ]);
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/employee/details/{userName}", name="employee_details")
     */
    public function detailsAction(Request $request)
    {
        $userName = $request->get('userName');

        if ($userName == 'admin') {
            $userName = $this->getUser()->getUserName();
        }

        /** @var User $user */
        $user = $this->getUserRepository()->findOneBy(['username' => $userName]);
        $projects = $this->getProjectRepository()->getAvailableProjects($user);

        $dayOffs = $this->getDayOffRepository()->findBy(['owner' => $user->getId()]);
        $userAchievements = $this->getUserAchievementRepository()->findBy(['owner' => $user]);
        $latestTasksDiffs = $this->getTaskDiffRepository()->getLatestChanges($user);
        $latestTasks = [];
        $basicAchievements = [];
        $projectAchievements = [];

        foreach ($userAchievements as $userAchievement) {
            if ($userAchievement->getAchievement()->isBasic()) {
                $basicAchievements[] = $userAchievement;
            } else {
                $projectAchievements[] = $userAchievement;
            }
        }

        foreach ($latestTasksDiffs as $diff) {
            /** @var TaskDiff $diff */
            if (!isset($latestTasks[$diff->getTask()->getId()])) {
                $latestTasks[$diff->getTask()->getId()] = $diff->getTask();
            }
        }

        return $this->render('employee/details.html.twig', [
            'user' => $user,
            'projects' => $projects,
            'latestTasks' => $latestTasks,
            'basicAchievements' => $basicAchievements,
            'projectAchievements' => $projectAchievements,
            'dayOffs' => $dayOffs
        ]);

    }

    /**
     * Company structure
     *
     * @Route("/company/structure", name="company_structure")
     */
    public function organisationalStructureAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $teams = $this->getTeamRepository()->findAll();

        if (!empty($filters['team'])) {
            $team = $this->getTeamRepository()->find($filters['team']);
        } else {
            $team = $this->getTeamRepository()->findOneBy(['type' => Team::TYPE_GENERAL_SERVICE]);
            $filters['team'] = $team->getId();
        }

        return $this->render('employee/company_structure.html.twig', [
            'filters' => $filters,
            'teams' => $teams,
            'team' => $team
        ]);
    }

    /**
     * Add employee
     *
     * @Route("/employee/add-employee", name="add_employee")
     */
    public function addEmployeeAction(Request $request)
    {
        if (!$this->getUser()->canEditEmployees()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $employeeDetails = $request->get('employee');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try {
            if ($this->getUser()->canEditEmployees()) {
                $employee = new User();

                $employee = $this->buildEmployee($employee, $employeeDetails);
                $password = $this->generatePassword();

                if (empty($employee->getId())) {
                    $employee->setPlainPassword($password);
                }

                $this->getEm()->persist($employee);
                $this->getEm()->flush();

                $this->sendEmail(
                    $employee->getEmail(),
                    $this->renderView(
                        'emails/employee/employee_password.html.twig', [
                        'password' => $password,
                        'login' => $employee->getUsername()
                    ])
                );
            }
        } catch(\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('employees_list');
    }

    /**
     * Edit employee
     *
     * @Route("/employee/edit-employee", name="edit_employee")
     */
    public function editEmployeeAction(Request $request)
    {
        if (!$this->getUser()->canEditEmployees()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $employeeId = $request->get('id');

        /* @var User $employee */
        $employee = $this->getUserRepository()->find($employeeId);
        $employeeDetails = $request->get('employee');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try {
            if ($this->getUser()->canEditEmployees()) {
                $employee = $this->buildEmployee($employee, $employeeDetails);

                $this->getEm()->persist($employee);
                $this->getEm()->flush();
            }
        } catch(\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('employees_list');
    }

    /**
     * @param User $employee
     * @param $employeeDetails
     * @return User
     * @throws \Exception
     */
    public function buildEmployee(User $employee, $employeeDetails)
    {
        $team = $this->getTeamRepository()->find($employeeDetails['team']);
        $employeeRole = $this->getProjectRoleRepository()->find($employeeDetails['employeeRole']);

        $employee
            ->setFirstname($employeeDetails['firstname'])
            ->setLastname($employeeDetails['lastname'])
            ->setDateOfBirth(new \DateTime ($employeeDetails['dateOfBirth']))
            ->setEmployeeStatus($employeeDetails['employeeStatus'])
            ->setEmploymentDate(new \DateTime($employeeDetails['employmentDate']))
            ->setRoom($employeeDetails['room'])
            ->setMiddlename($employeeDetails['middlename'])
            ->setTeam($team)
            ->setEmployeeRole($employeeRole)
            ->setGender($employeeDetails['gender'])
            ->setPhone($employeeDetails['phone'])
            ->setEmail($employeeDetails['email'])
            ->setUserName($employeeDetails['userName'])
        ;

        return $employee;
    }

    protected function generatePassword()
    {
        $arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        do {
            $password = "";
            for ($i = 0; $i < 10; $i++) {
                $index = rand(0, count($arr) - 1);
                $password .= $arr[$index];
            }
        } while (!(preg_match('/[A-Z]+/', $password) && preg_match('/[a-z]+/', $password) && preg_match('/[0-9]+/', $password)));

        return $password;
    }

    /**
     * Finds and displays all employees.
     *
     * @Route("/employees/birthdates", name="employees_birthdates")
     */
    public function birthDatesAction()
    {
        $employees = $this->getUserRepository()->getEmployeesBirthdates();
        return $this->render('employee/birthdates.html.twig', [
            'employees' => $employees,
        ]);
    }

    /**
     * Get last online ajax.
     *
     * @Route("/last-online", name="employee_last_online")
     */
    public function getLastOnline()
    {
        /** @var User $employee */
        $employee = $this->getUserRepository()->find($this->getUser());

        $employee->setLastOnline(new \DateTime());

        $this->getEm()->persist($employee);
        $this->getEm()->flush();

        return new Response($employee->getLastOnline()->format('y/m/d h:i:s'));
    }

    protected function sendEmail($recipient, $body)
    {
        $email = new \Swift_Message('[OLYMP]{Регистрация} ');
        $email
            ->setFrom('olymp@npo-at.com')
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        $this->get('mailer')->send($email);
    }
}
